<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function history(Request $request)
    {
        // Auto expire booking pending > 30 menit
        $expiredBookings = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('status_bookings', 'Pending')
            ->where('created_at', '<=', now()->subMinutes(30))
            ->get();

        foreach ($expiredBookings as $booking) {

            // Ubah status booking
            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => 'Expired - payment exceeded 30 minutes'
            ]);

            // Ubah status payment
            if ($booking->payment) {
                $booking->payment->update([
                    'status_payments' => 'expired'
                ]);
            }

            // Buka kembali jadwal
            $this->freeSchedules($booking);
        }

        $tab = $request->query('tab', 'all');

        $query = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $bookings = $query->map(function($booking) {
            $computed = $booking->computed_status;
            
            if ($computed === 'Waiting for Payment') $booking->custom_status = 'waiting';
            elseif ($computed === 'Rescheduled') $booking->custom_status = 'reschedule';
            elseif ($computed === 'Canceled') $booking->custom_status = 'canceled';
            elseif ($computed === 'Done') $booking->custom_status = 'done';
            elseif ($computed === 'Paid') $booking->custom_status = 'paid';
            else $booking->custom_status = 'unknown';

            return $booking;
        });

        if ($tab !== 'all') {
            $bookings = $bookings->filter(fn($b) => $b->custom_status === $tab);
        }

        return view('user.history', compact('bookings', 'tab'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:schedules,id_schedules',
            'play_date' => 'required|date|after_or_equal:today',
            'subcourt_name' => 'required|string',
        ]);

        $schedules = Schedule::query()
            ->with('field')
            ->whereIn('id_schedules', $request->schedule_ids)
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return back()->with('error', 'Schedule not found.');
        }

        $totalPrice = 0;
        $firstFieldId = $schedules->first()->field->id_fields;

        $existingBookings = Booking::where('subcourt_name', $request->subcourt_name)
            ->where('play_date', $request->play_date)
            ->whereIn('status_bookings', ['Pending', 'Paid', 'Done'])
            ->get();

        foreach ($schedules as $schedule) {
            if (strtolower(trim($schedule->status_schedules)) === 'locked') {
                return back()->with('error', 'Sorry, this schedule slot is locked.');
            }
            $isBooked = false;
            foreach ($existingBookings as $b) {
                $ids = $b->schedule_ids ?? [];
                if (in_array((string)$schedule->id_schedules, array_map('strval', $ids))) {
                    $isBooked = true;
                    break;
                }
            }
            if ($isBooked) {
                return back()->with('error', 'Sorry, one of the schedules you selected was just booked by someone else.');
            }

            if ($schedule->field->id_fields !== $firstFieldId) {
                return back()->with('error', 'All schedules must be from the same field.');
            }


            $totalPrice += $schedule->field->price_per_hour;
        }

        $firstSchedule = $schedules->first();
        $rescheduleId = session('reschedule_booking_id');

        if ($rescheduleId) {
            $oldBooking = Booking::findOrFail($rescheduleId);
            
            if ($totalPrice > $oldBooking->total_price) {
                return back()->with('error', 'The duration of the new schedule exceeds your reschedule limit. Select a duration equal to or less than Rp ' . number_format($oldBooking->total_price, 0, ',', '.') . ').');
            }

            $bookingCode = '#AC-RES-' . strtoupper(Str::random(4));
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'schedule_id' => $firstSchedule->id_schedules,
                'schedule_ids' => $request->schedule_ids,
                'subcourt_name' => $request->subcourt_name,
                'booking_code' => $bookingCode,
                'total_price' => $totalPrice,
                'status_bookings' => 'Paid',
                'play_date' => $request->play_date,
            ]);



            if ($oldBooking->payment) {
                $newPayment = $oldBooking->payment->replicate();
                $newPayment->booking_id = $booking->id_bookings;
                $newPayment->midtrans_order_id = 'AC-RES-' . $booking->id_bookings . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
                $newPayment->save();
            }

            $oldBooking->update([
                'status_bookings' => 'Cancelled',
                'cancel_reason' => 'Rescheduled'
            ]);
            $this->freeSchedules($oldBooking);

            session()->forget('reschedule_booking_id');
            return redirect()->route('user.history', ['tab' => 'paid'])->with('success_reschedule', 'Schedule rescheduled successfully!');
        }

        $bookingCode = '#AC-' . strtoupper(Str::random(6));

        $booking = Booking::query()->create([
            'user_id' => Auth::id(),
            'schedule_id' => $firstSchedule->id_schedules,
            'schedule_ids' => $request->schedule_ids,
            'subcourt_name' => $request->subcourt_name,
            'booking_code' => $bookingCode,
            'total_price' => $totalPrice,
            'status_bookings' => 'Pending',
            'play_date' => $request->play_date,
        ]);



        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
        \Midtrans\Config::$curlOptions = [
            CURLOPT_HTTPHEADER => [],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 20,
        ];

        $midtransOrderId = 'AC-' . $booking->id_bookings . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $params = array(
            'transaction_details' => array(
                'order_id' => $midtransOrderId,
                'gross_amount' => $totalPrice,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name_users,
                'email' => Auth::user()->email,
            ),
        );

        $midtransStartedAt = microtime(true);
        Log::info('Midtrans Snap token generation started', [
            'booking_id' => $booking->id_bookings,
            'booking_code' => $bookingCode,
        ]);

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            Log::warning('Midtrans Snap token generation failed', [
                'booking_id' => $booking->id_bookings,
                'booking_code' => $bookingCode,
                'duration_ms' => (int) round((microtime(true) - $midtransStartedAt) * 1000),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            $booking->delete();

            return response()->json([
                'success' => false,
                'message' => 'Payment service is temporarily unavailable. Please try again.',
            ], 502);
        }

        Log::info('Midtrans Snap token generation succeeded', [
            'booking_id' => $booking->id_bookings,
            'booking_code' => $bookingCode,
            'duration_ms' => (int) round((microtime(true) - $midtransStartedAt) * 1000),
        ]);

        Payment::query()->create([
            'booking_id' => $booking->id_bookings,
            'midtrans_order_id' => $midtransOrderId,
            'amount' => $totalPrice,
            'status_payments' => 'pending',
            'snap_token' => $snapToken,
        ]);

        return redirect()->route('booking.checkout', $booking->id_bookings);
    }

    public function cancel(Request $request, $id)
    {
        $booking = Booking::query()
                          ->with('schedule.field')
                          ->where('id_bookings', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        $request->validate([
            'cancel_reason' => 'required|string|max:255'
        ]);

        if ($booking->status_bookings === 'Cancelled') {
            return back()->with('error', 'This booking has already been cancelled.');
        }

        try {
            $this->freeSchedules($booking);
        } catch (\Exception $e) {
            Log::error('Cancel freeSchedules error: ' . $e->getMessage(), [
                'booking_id' => $booking->id_bookings
            ]);
        }

        if ($booking->status_bookings === 'Pending') {
            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason
            ]);
            return back()->with('success_cancel', 'Booking has been canceled.');
        }

        if ($booking->status_bookings === 'Paid') {
            $playDate = Carbon::parse($booking->play_date);
            $today = Carbon::now();
            $daysDifference = $today->diffInDays($playDate, false);

            if ($daysDifference < 3) {
                return back()->with('error', 'Cancellation failed. Paid bookings can only be cancelled at least 3 days (H-3) before the play date.');
            }

            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason
            ]);
            return back()->with('success_cancel', 'Booking has been canceled and your money is refunded.');
        }

        return back()->with('error', 'Booking cannot be cancelled.');
    }

    public function reschedule(Request $request, $id)
    {
        $booking = Booking::query()
                          ->with('schedule.field')
                          ->where('id_bookings', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        if ($booking->status_bookings !== 'Paid') {
            return back()->with('error', 'Only paid bookings can be rescheduled.');
        }

        $playDate = Carbon::parse($booking->play_date);
        $today = Carbon::now();
        $daysDifference = $today->diffInDays($playDate, false);

        if ($daysDifference < 3 && !Carbon::parse($booking->play_date)->isPast()) {
            return back()->with('error', 'Reschedule failed. Can only be done at least 3 days (H-3) before the play date.');
        }

        session(['reschedule_booking_id' => $booking->id_bookings]);

        // Redirect to the specific field page so they can pick a new date/time
        return redirect()->route('fields.show', $booking->schedule->field_id ?? $booking->getSchedulesList()->first()->field_id)
            ->with('info', 'Select a new schedule. You are currently in Reschedule mode (Max Rp ' . number_format($booking->total_price, 0, ',', '.') . ').');
    }

    /**
     * Free the schedules associated with a booking.
     */
    private function freeSchedules($booking)
    {
        // No longer needed because availability is calculated dynamically from bookings
    }

    public function invoice($id)
    {
        $booking = Booking::query()
            ->with(['schedule.field', 'payment', 'user'])
            ->where('id_bookings', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status_bookings !== 'Paid') {
            return redirect()->route('user.history')->with('error', 'Invoice is only available for paid bookings.');
        }

        return view('user.invoice', compact('booking'));
    }

    public function checkout($id)
    {
        $booking = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('id_bookings', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $snapToken = $booking->payment?->snap_token;

        if (!$snapToken) {
            return redirect()->route('user.history')->with('error', 'Payment token not found. Please try booking again.');
        }

        return view('fields.checkout', compact('booking', 'snapToken'));
    }

    public function show($id)
    {
        $booking = Booking::with([
            'schedule.field',
            'payment',
            'user'
        ])->findOrFail($id);

        return view('user.booking-detail', compact('booking'));
    }

    public function apiHistory(Request $request)
    {
        // Auto expire booking pending > 30 menit
        $expiredBookings = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('status_bookings', 'Pending')
            ->where('created_at', '<=', now()->subMinutes(30))
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => 'Expired - payment exceeded 30 minutes'
            ]);

            if ($booking->payment) {
                $booking->payment->update([
                    'status_payments' => 'expired'
                ]);
            }
            $this->freeSchedules($booking);
        }

        $tab = $request->query('tab', 'all');

        $query = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $bookings = $query->map(function($booking) {
            $computed = $booking->computed_status;
            
            if ($computed === 'Waiting for Payment') $booking->custom_status = 'waiting';
            elseif ($computed === 'Rescheduled') $booking->custom_status = 'reschedule';
            elseif ($computed === 'Canceled') $booking->custom_status = 'canceled';
            elseif ($computed === 'Done') $booking->custom_status = 'done';
            elseif ($computed === 'Paid') $booking->custom_status = 'paid';
            else $booking->custom_status = 'unknown';

            return $booking;
        });

        if ($tab !== 'all') {
            $bookings = $bookings->filter(fn($b) => $b->custom_status === $tab)->values();
        }

        return response()->json([
            'success' => true,
            'bookings' => $bookings,
            'tab' => $tab
        ]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'schedule_ids' => 'required|array|min:1',
            'schedule_ids.*' => 'exists:schedules,id_schedules',
            'play_date' => 'required|date|after_or_equal:today',
            'subcourt_name' => 'required|string',
            'reschedule_booking_id' => 'nullable|integer',
        ]);

        $schedules = Schedule::query()
            ->with('field')
            ->whereIn('id_schedules', $request->schedule_ids)
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Schedule not found.'], 404);
        }

        $totalPrice = 0;
        $firstFieldId = $schedules->first()->field->id_fields;

        $existingBookings = Booking::where('subcourt_name', $request->subcourt_name)
            ->where('play_date', $request->play_date)
            ->whereIn('status_bookings', ['Pending', 'Paid', 'Done'])
            ->get();

        foreach ($schedules as $schedule) {
            if (strtolower(trim($schedule->status_schedules)) === 'locked') {
                return response()->json(['success' => false, 'message' => 'Sorry, this schedule slot is locked.'], 422);
            }
            $isBooked = false;
            foreach ($existingBookings as $b) {
                $ids = $b->schedule_ids ?? [];
                if (in_array((string)$schedule->id_schedules, array_map('strval', $ids))) {
                    $isBooked = true;
                    break;
                }
            }
            if ($isBooked) {
                return response()->json(['success' => false, 'message' => 'Sorry, one of the schedules you selected was just booked by someone else.'], 422);
            }

            if ($schedule->field->id_fields !== $firstFieldId) {
                return response()->json(['success' => false, 'message' => 'All schedules must be from the same field.'], 422);
            }

            $totalPrice += $schedule->field->price_per_hour;
        }

        $firstSchedule = $schedules->first();
        $rescheduleId = $request->reschedule_booking_id;

        if ($rescheduleId) {
            $oldBooking = Booking::findOrFail($rescheduleId);
            
            if ($totalPrice > $oldBooking->total_price) {
                return response()->json(['success' => false, 'message' => 'The duration of the new schedule exceeds your reschedule limit.'], 422);
            }

            $bookingCode = '#AC-RES-' . strtoupper(Str::random(4));
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'schedule_id' => $firstSchedule->id_schedules,
                'schedule_ids' => $request->schedule_ids,
                'subcourt_name' => $request->subcourt_name,
                'booking_code' => $bookingCode,
                'total_price' => $totalPrice,
                'status_bookings' => 'Paid',
                'play_date' => $request->play_date,
            ]);



            if ($oldBooking->payment) {
                $newPayment = $oldBooking->payment->replicate();
                $newPayment->booking_id = $booking->id_bookings;
                $newPayment->midtrans_order_id = 'AC-RES-' . $booking->id_bookings . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
                $newPayment->save();
            }

            $oldBooking->update([
                'status_bookings' => 'Cancelled',
                'cancel_reason' => 'Rescheduled'
            ]);
            $this->freeSchedules($oldBooking);

            return response()->json([
                'success' => true,
                'message' => 'Schedule rescheduled successfully!',
                'booking' => $booking
            ]);
        }

        $bookingCode = '#AC-' . strtoupper(Str::random(6));

        $booking = Booking::query()->create([
            'user_id' => Auth::id(),
            'schedule_id' => $firstSchedule->id_schedules,
            'schedule_ids' => $request->schedule_ids,
            'subcourt_name' => $request->subcourt_name,
            'booking_code' => $bookingCode,
            'total_price' => $totalPrice,
            'status_bookings' => 'Pending',
            'play_date' => $request->play_date,
        ]);



        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds');
        \Midtrans\Config::$curlOptions = [
            CURLOPT_HTTPHEADER => [],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 20,
        ];

        $midtransOrderId = 'AC-' . $booking->id_bookings . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $params = array(
            'transaction_details' => array(
                'order_id' => $midtransOrderId,
                'gross_amount' => $totalPrice,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name_users,
                'email' => Auth::user()->email,
            ),
        );

        $midtransStartedAt = microtime(true);
        Log::info('Midtrans Snap token generation started', [
            'booking_id' => $booking->id_bookings,
            'booking_code' => $bookingCode,
        ]);

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        } catch (\Throwable $e) {
            Log::warning('Midtrans Snap token generation failed', [
                'booking_id' => $booking->id_bookings,
                'booking_code' => $bookingCode,
                'duration_ms' => (int) round((microtime(true) - $midtransStartedAt) * 1000),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            $booking->delete();

            return response()->json([
                'success' => false,
                'message' => 'Payment service is temporarily unavailable. Please try again.',
            ], 502);
        }

        Log::info('Midtrans Snap token generation succeeded', [
            'booking_id' => $booking->id_bookings,
            'booking_code' => $bookingCode,
            'duration_ms' => (int) round((microtime(true) - $midtransStartedAt) * 1000),
        ]);

        $payment = Payment::query()->create([
            'booking_id' => $booking->id_bookings,
            'midtrans_order_id' => $midtransOrderId,
            'amount' => $totalPrice,
            'status_payments' => 'pending',
            'snap_token' => $snapToken,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully!',
            'booking' => $booking,
            'snapToken' => $snapToken
        ]);
    }

    public function apiCancel(Request $request, $id)
    {
        $booking = Booking::query()
                          ->with('schedule.field')
                          ->where('id_bookings', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        $request->validate([
            'cancel_reason' => 'required|string|max:255'
        ]);

        if ($booking->status_bookings === 'Cancelled') {
            return response()->json(['success' => false, 'message' => 'This booking has already been cancelled.'], 422);
        }

        try {
            $this->freeSchedules($booking);
        } catch (\Exception $e) {
            Log::error('Cancel freeSchedules error: ' . $e->getMessage());
        }

        if ($booking->status_bookings === 'Pending') {
            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason
            ]);
            return response()->json(['success' => true, 'message' => 'Booking has been canceled.', 'booking' => $booking]);
        }

        if ($booking->status_bookings === 'Paid') {
            $playDate = Carbon::parse($booking->play_date);
            $today = Carbon::now();
            $daysDifference = $today->diffInDays($playDate, false);

            if ($daysDifference < 3) {
                return response()->json(['success' => false, 'message' => 'Cancellation failed. Paid bookings can only be cancelled at least 3 days (H-3) before the play date.'], 422);
            }

            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $request->cancel_reason
            ]);
            return response()->json(['success' => true, 'message' => 'Booking has been canceled and your money is refunded.', 'booking' => $booking]);
        }

        return response()->json(['success' => false, 'message' => 'Booking cannot be cancelled.'], 422);
    }

    public function apiReschedule(Request $request, $id)
    {
        $booking = Booking::query()
                          ->with('schedule.field')
                          ->where('id_bookings', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

        if ($booking->status_bookings !== 'Paid') {
            return response()->json(['success' => false, 'message' => 'Only paid bookings can be rescheduled.'], 422);
        }

        $playDate = Carbon::parse($booking->play_date);
        $today = Carbon::now();
        $daysDifference = $today->diffInDays($playDate, false);

        if ($daysDifference < 3 && !Carbon::parse($booking->play_date)->isPast()) {
            return response()->json(['success' => false, 'message' => 'Reschedule failed. Can only be done at least 3 days (H-3) before the play date.'], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Eligible for reschedule',
            'booking' => $booking
        ]);
    }

    public function apiCheckout(Request $request, $id)
    {
        $booking = Booking::query()
            ->with(['schedule.field', 'payment'])
            ->where('id_bookings', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $snapToken = $booking->payment?->snap_token;

        if (!$snapToken) {
            return response()->json(['success' => false, 'message' => 'Payment token not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'booking' => $booking,
            'snapToken' => $snapToken
        ]);
    }

    public function apiShowDetail(Request $request, $id)
    {
        $booking = Booking::with([
            'schedule.field',
            'payment',
            'user'
        ])->where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }

    public function apiInvoice(Request $request, $id)
    {
        $booking = Booking::query()
            ->with(['schedule.field', 'payment', 'user'])
            ->where('id_bookings', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($booking->status_bookings !== 'Paid') {
            return response()->json(['success' => false, 'message' => 'Invoice is only available for paid bookings.'], 400);
        }

        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }
}
