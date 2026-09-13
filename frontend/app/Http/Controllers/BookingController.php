<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class BookingController extends Controller
{
    public function history(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/user/history', $request->all());

        if (!$response->successful()) {
            if ($response->status() === 401) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            $errorMsg = $response->json('message') ?? 'Failed to retrieve booking history. (Status: ' . $response->status() . ')';
            return redirect()->route('home')->with('error', $errorMsg);
        }

        $data = $response->json();
        $tab = $data['tab'] ?? 'all';
        $bookingsData = $data['bookings'] ?? [];
        
        // Hydrate collection of bookings
        $bookings = collect($bookingsData)->map(function ($item) {
            return $this->hydrateBooking($item);
        });

        return view('user.history', compact('bookings', 'tab'));
    }

    public function store(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();
        $rescheduleId = session('reschedule_booking_id');

        try {
            $response = Http::withToken($token)->post($backendUrl . '/booking', array_merge($request->all(), [
                'reschedule_booking_id' => $rescheduleId
            ]));
        } catch (ConnectionException|RequestException $e) {
            return back()->with('error', 'Booking service is temporarily unavailable. Please try again.');
        }

        if ($response->successful()) {
            $booking = $response->json('booking');
            
            if ($rescheduleId) {
                session()->forget('reschedule_booking_id');
                return redirect()->route('user.history', ['tab' => 'paid'])->with('success_reschedule', 'Schedule rescheduled successfully!');
            }

            $bookingId = $booking['id_bookings'] ?? null;
            if ($bookingId) {
                return redirect()->route('booking.checkout', $bookingId);
            }
        }

        if ($response->status() === 401) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $error = $response->json('message') ?? 'An error occurred during booking. (Status: ' . $response->status() . ')';
        return back()->with('error', $error);
    }

    public function cancel(Request $request, $id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/booking/' . $id . '/cancel', [
            'cancel_reason' => $request->cancel_reason
        ]);

        if ($response->successful()) {
            $message = $response->json('message') ?? 'Booking has been cancelled.';
            return back()->with('success_cancel', $message);
        }

        if ($response->status() === 401) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $error = $response->json('message') ?? 'Failed to cancel booking. (Status: ' . $response->status() . ')';
        return back()->with('error', $error);
    }

    public function reschedule(Request $request, $id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/booking/' . $id . '/reschedule');

        if ($response->successful()) {
            session(['reschedule_booking_id' => $id]);
            
            $booking = $response->json('booking');
            $fieldId = $booking['schedule']['field_id'] ?? 1;

            return redirect()->route('fields.show', $fieldId)
                ->with('info', 'Select a new schedule. You are currently in Reschedule mode (Max Rp ' . number_format($booking['total_price'] ?? 0, 0, ',', '.') . ').');
        }

        if ($response->status() === 401) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        }

        $error = $response->json('message') ?? 'Failed to initiate reschedule. (Status: ' . $response->status() . ')';
        return back()->with('error', $error);
    }

    public function checkout($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/booking/' . $id . '/checkout');

        if (!$response->successful()) {
            if ($response->status() === 401) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            return redirect()->route('user.history')->with('error', $response->json('message') ?? 'Payment token not found.');
        }

        $booking = $this->hydrateBooking($response->json('booking'));
        $snapToken = $response->json('snapToken');

        return view('fields.checkout', compact('booking', 'snapToken'));
    }

    public function show($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/booking/' . $id);

        if (!$response->successful()) {
            if ($response->status() === 401) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            abort($response->status());
        }

        $booking = $this->hydrateBooking($response->json('booking'));

        return view('user.booking-detail', compact('booking'));
    }

    public function invoice($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/booking/' . $id . '/invoice');

        if (!$response->successful()) {
            if ($response->status() === 401) {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }
            return redirect()->route('user.history')->with('error', $response->json('message') ?? 'Invoice is only available for paid bookings.');
        }

        $booking = $this->hydrateBooking($response->json('booking'));

        return view('user.invoice', compact('booking'));
    }

    /**
     * Get token from session
     */
    private function getToken()
    {
        return session('api_token');
    }

    /**
     * Helper to hydrate raw backend array data into a Booking Eloquent model
     */
    private function hydrateBooking($item)
    {
        if (!$item) return null;

        $booking = new \App\Models\Booking();
        $booking->exists = true;

        $attributes = collect($item)->except(['schedule', 'payment', 'user'])->toArray();
        $booking->forceFill($attributes);

        if (isset($item['schedule'])) {
            $schedule = new \App\Models\Schedule();
            $schedule->exists = true;
            
            $scheduleAttributes = collect($item['schedule'])->except(['field'])->toArray();
            $schedule->forceFill($scheduleAttributes);
            
            if (isset($item['schedule']['field'])) {
                $field = new \App\Models\Field();
                $field->exists = true;
                
                $fieldAttributes = collect($item['schedule']['field'])->except(['facilities', 'schedules'])->toArray();
                $field->forceFill($fieldAttributes);
                
                $schedule->setRelation('field', $field);
            }
            
            $booking->setRelation('schedule', $schedule);
        }

        if (isset($item['payment'])) {
            $payment = new \App\Models\Payment();
            $payment->exists = true;
            $payment->forceFill($item['payment']);
            $booking->setRelation('payment', $payment);
        }

        if (isset($item['user'])) {
            $user = new \App\Models\User();
            $user->exists = true;
            $user->forceFill($item['user']);
            $booking->setRelation('user', $user);
        }

        return $booking;
    }
}
