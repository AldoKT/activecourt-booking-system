<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use App\Models\User;
use App\Models\Payment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalRevenue' => Payment::query()
                ->where('status_payments', 'settlement')
                ->sum('amount') ?? 0,

            'activeBookings' => Booking::query()
                ->where('status_bookings', 'Paid')
                ->count(),

            'newUsers' => User::query()
                ->where('created_at', '>=', now()->subMonth())
                ->count(),

            'conversionRate' => 68.5,

            'recentBookings' => Booking::query()
                ->with(['user', 'schedule.field'])
                ->latest()
                ->take(5)
                ->get(),

            'totalFields' => Field::query()->count(),

            'totalUsers' => User::query()->count(),

            'todayBookings' => Booking::query()
                ->whereDate('created_at', today())
                ->count(),

            'todayRevenue' => Payment::query()
                ->whereDate('created_at', today())
                ->where('status_payments', 'settlement')
                ->sum('amount') ?? 0,

            'pendingBookings' => Booking::query()
                ->where('status_bookings', 'Pending')
                ->count(),
        ]);
    }

    // ==================== FIELDS ====================

    public function fields()
    {
        $fields = Field::query()->with('facilities')->latest()->get();
        return view('admin.fields', compact('fields'));
    }

    public function storeField(Request $request)
    {
        $request->validate([
            'name_fields' => 'required|string|max:255',
            'type_fields' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'sub_courts' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $subCourts = array_map('trim', explode(',', $request->sub_courts));
        $subCourts = array_filter($subCourts);
        if (empty($subCourts)) {
            return back()->with('error', 'Sub-courts must be filled in a comma-separated format, at least 1 item.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('fields', 'public');
        }

        $field = Field::query()->create([
            'name_fields' => $request->name_fields,
            'type_fields' => $request->type_fields,
            'address' => $request->address,
            'description' => $request->description ?? '',
            'price_per_hour' => $request->price_per_hour,
            'capacity' => $request->capacity,
            'image' => $imagePath,
            'sub_courts' => $subCourts,
        ]);

        $iconMap = [
            'Free WiFi' => 'wifi',
            'WiFi' => 'wifi',
            'Shower Room' => 'shower',
            'Shower' => 'shower',
            'Secure Parking' => 'parking',
            'Parking' => 'parking',
            'Full AC Area' => 'snowflake',
            'AC' => 'snowflake',
            'Locker Room' => 'key',
            'Locker' => 'key',
            'Canteen' => 'coffee',
            'Cafe' => 'coffee',
            'Lightings' => 'lightbulb',
            'Lights' => 'lightbulb',
            'Toilet' => 'toilet',
            'Prayer Room' => 'mosque',
            'Mushola' => 'mosque',
            'First Aid' => 'first-aid',
        ];

        if ($request->has('facilities')) {
            foreach ($request->facilities as $facName) {
                if (empty(trim($facName))) continue;
                $icon = 'check-circle';
                foreach ($iconMap as $key => $val) {
                    if (stripos($facName, $key) !== false) {
                        $icon = $val;
                        break;
                    }
                }
                $field->facilities()->create([
                    'name_facilities' => trim($facName),
                    'icon' => $icon,
                ]);
            }
        }

        if ($request->has('custom_facilities') && !empty($request->custom_facilities)) {
            $customFacs = array_map('trim', explode(',', $request->custom_facilities));
            foreach ($customFacs as $facName) {
                if (empty($facName)) continue;
                $icon = 'check-circle';
                foreach ($iconMap as $key => $val) {
                    if (stripos($facName, $key) !== false) {
                        $icon = $val;
                        break;
                    }
                }
                $field->facilities()->create([
                    'name_facilities' => $facName,
                    'icon' => $icon,
                ]);
            }
        }

        return redirect()->route('admin.fields')->with('success', 'Field added successfully.');
    }

    public function editField($id)
    {
        $field = Field::query()->findOrFail($id);
        return view('admin.field-edit', compact('field'));
    }

    public function updateField(Request $request, $id)
    {
        $field = Field::query()->findOrFail($id);

        $request->validate([
            'name_fields' => 'required|string|max:255',
            'type_fields' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'sub_courts' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $subCourts = array_map('trim', explode(',', $request->sub_courts));
        $subCourts = array_filter($subCourts);
        if (empty($subCourts)) {
            return back()->with('error', 'Sub-courts must be filled in a comma-separated format, at least 1 item.');
        }

        $data = [
            'name_fields' => $request->name_fields,
            'type_fields' => $request->type_fields,
            'address' => $request->address,
            'description' => $request->description ?? '',
            'price_per_hour' => $request->price_per_hour,
            'capacity' => $request->capacity,
            'sub_courts' => $subCourts,
        ];

        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($field->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($field->image);
            }
            $imagePath = $request->file('image')->store('fields', 'public');
            $data['image'] = $imagePath;
        }

        $field->update($data);

        $field->facilities()->delete();

        $iconMap = [
            'Free WiFi' => 'wifi',
            'WiFi' => 'wifi',
            'Shower Room' => 'shower',
            'Shower' => 'shower',
            'Secure Parking' => 'parking',
            'Parking' => 'parking',
            'Full AC Area' => 'snowflake',
            'AC' => 'snowflake',
            'Locker Room' => 'key',
            'Locker' => 'key',
            'Canteen' => 'coffee',
            'Cafe' => 'coffee',
            'Lightings' => 'lightbulb',
            'Lights' => 'lightbulb',
            'Toilet' => 'toilet',
            'Prayer Room' => 'mosque',
            'Mushola' => 'mosque',
            'First Aid' => 'first-aid',
        ];

        if ($request->has('facilities')) {
            foreach ($request->facilities as $facName) {
                if (empty(trim($facName))) continue;
                $icon = 'check-circle';
                foreach ($iconMap as $key => $val) {
                    if (stripos($facName, $key) !== false) {
                        $icon = $val;
                        break;
                    }
                }
                $field->facilities()->create([
                    'name_facilities' => trim($facName),
                    'icon' => $icon,
                ]);
            }
        }

        if ($request->has('custom_facilities') && !empty($request->custom_facilities)) {
            $customFacs = array_map('trim', explode(',', $request->custom_facilities));
            foreach ($customFacs as $facName) {
                if (empty($facName)) continue;
                $icon = 'check-circle';
                foreach ($iconMap as $key => $val) {
                    if (stripos($facName, $key) !== false) {
                        $icon = $val;
                        break;
                    }
                }
                $field->facilities()->create([
                    'name_facilities' => $facName,
                    'icon' => $icon,
                ]);
            }
        }

        return redirect()->route('admin.fields')->with('success', 'Field updated successfully.');
    }

    public function destroyField($id)
    {
        $field = Field::query()->with('schedules')->findOrFail($id);

        // Check if there are active schedules
        $activeSchedules = $field->schedules()->where('status_schedules', 'Booked')->count();
        if ($activeSchedules > 0) {
            return back()->with('error', 'Cannot delete field with active bookings.');
        }

        if ($field->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($field->image);
        }

        $field->delete();
        return redirect()->route('admin.fields')->with('success', 'Field deleted successfully.');
    }

    // ==================== USERS ====================

    public function users()
    {
        $users = User::query()->latest()->get();
        return view('admin.users', [
            'users' => $users,
            'totalUsers' => $users->count(),
            'activeNow' => User::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'banned' => User::query()->whereNotNull('banned_at')->count(),
        ]);
    }

    public function editUser($id)
    {
        $user = User::query()->findOrFail($id);
        return view('admin.user-edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::query()->findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id . ',id_users',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',id_users',
            'role' => 'required|in:admin,user,guest',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users')->with('success', 'User data updated successfully.');
    }

    public function toggleBanUser($id)
    {
        $user = User::query()->findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot ban admin users.');
        }

        if ($user->banned_at) {
            $user->update(['banned_at' => null]);
            return redirect()->route('admin.users')->with('success', "User {$user->username} has been successfully unbanned.");
        } else {
            $user->update(['banned_at' => now()]);
            return redirect()->route('admin.users')->with('success', "User {$user->username} has been successfully banned.");
        }
    }

    // ==================== BOOKINGS ====================

    public function bookings()
    {
        $bookings = Booking::query()->with(['user', 'schedule.field', 'payment'])->latest()->get();
        return view('admin.bookings', compact('bookings'));
    }

    public function bookingDetail($id)
    {
        $booking = Booking::with([
            'schedule.field',
            'payment',
            'user'
        ])->findOrFail($id);

        return view('admin.booking-detail', compact('booking'));
    }

    public function bookingInvoice($id)
    {
        $booking = Booking::query()
            ->with(['schedule.field', 'payment', 'user'])
            ->findOrFail($id);

        return view('user.invoice', compact('booking'));
    }

    public function forcePaid($id)
    {
        $booking = Booking::query()->findOrFail($id);

        if ($booking->status_bookings === 'Paid') {
            return back()->with('info', 'This booking status is already Paid.');
        }

        $booking->update([
            'status_bookings' => 'Paid'
        ]);

        Payment::where('booking_id', $id)->update([
            'status_payments' => 'settlement',
            'paid_at' => now()
        ]);

        return redirect()->route('admin.bookings')->with('success', "Booking {$booking->booking_code} has been successfully forced to Paid.");
    }

    public function cancelBooking($id)
    {
        $booking = Booking::query()->with('schedule.field')->findOrFail($id);

        if ($booking->status_bookings === 'Cancelled') {
            return back()->with('error', 'This booking has already been cancelled.');
        }

        $booking->update([
            'status_bookings' => 'Cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => 'Cancelled by admin',
        ]);

        // Free schedules
        $this->freeSchedules($booking);

        return redirect()->route('admin.bookings')->with('success', "Booking {$booking->booking_code} has been successfully cancelled.");
    }

    /**
     * Free the schedules associated with a booking.
     */
    private function freeSchedules($booking)
    {
        $scheduleIdsToFree = $booking->schedule_ids;

        if (!empty($scheduleIdsToFree) && is_array($scheduleIdsToFree)) {
            Schedule::query()
                ->whereIn('id_schedules', $scheduleIdsToFree)
                ->update(['status_schedules' => 'Available']);
            return;
        }

        $schedule = $booking->schedule;
        if (!$schedule || !$schedule->field) return;

        $pricePerHour = $schedule->field->price_per_hour;
        $duration = ($pricePerHour <= 0) ? 1 : round($booking->total_price / $pricePerHour);

        $schedulesToFree = Schedule::query()
            ->where('field_id', $schedule->field_id)
            ->where('date', $booking->play_date)
            ->where('start_time', '>=', $schedule->start_time)
            ->orderBy('start_time')
            ->limit($duration)
            ->pluck('id_schedules');

        if ($schedulesToFree->isEmpty()) return;

        Schedule::query()
            ->whereIn('id_schedules', $schedulesToFree)
            ->update(['status_schedules' => 'Available']);
    }

    public function apiDashboard()
    {
        return response()->json([
            'totalRevenue' => Payment::query()
                ->where('status_payments', 'settlement')
                ->sum('amount') ?? 0,

            'activeBookings' => Booking::query()
                ->where('status_bookings', 'Paid')
                ->count(),

            'newUsers' => User::query()
                ->where('created_at', '>=', now()->subMonth())
                ->count(),

            'conversionRate' => 68.5,

            'recentBookings' => Booking::query()
                ->with(['user', 'schedule.field'])
                ->latest()
                ->take(5)
                ->get(),

            'totalFields' => Field::query()->count(),

            'totalUsers' => User::query()->count(),

            'todayBookings' => Booking::query()
                ->whereDate('created_at', today())
                ->count(),

            'todayRevenue' => Payment::query()
                ->whereDate('created_at', today())
                ->where('status_payments', 'settlement')
                ->sum('amount') ?? 0,

            'pendingBookings' => Booking::query()
                ->where('status_bookings', 'Pending')
                ->count(),
        ]);
    }

    public function apiFields()
    {
        $fields = Field::query()->with('facilities')->latest()->get();
        return response()->json($fields);
    }

    public function apiStoreField(Request $request)
    {
        $request->validate([
            'name_fields' => 'required|string|max:255',
            'type_fields' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'sub_courts' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $subCourts = array_map('trim', explode(',', $request->sub_courts));
        $subCourts = array_filter($subCourts);
        if (empty($subCourts)) {
            return response()->json(['success' => false, 'message' => 'Sub-courts must be filled in a comma-separated format, at least 1 item.'], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('fields', 'public');
        }

        $field = Field::query()->create([
            'name_fields' => $request->name_fields,
            'type_fields' => $request->type_fields,
            'address' => $request->address,
            'description' => $request->description ?? '',
            'price_per_hour' => $request->price_per_hour,
            'capacity' => $request->capacity,
            'image' => $imagePath,
            'sub_courts' => $subCourts,
        ]);

        $iconMap = [
            'Free WiFi' => 'wifi', 'WiFi' => 'wifi', 'Shower Room' => 'shower', 'Shower' => 'shower',
            'Secure Parking' => 'parking', 'Parking' => 'parking', 'Full AC Area' => 'snowflake', 'AC' => 'snowflake',
            'Locker Room' => 'key', 'Locker' => 'key', 'Canteen' => 'coffee', 'Cafe' => 'coffee',
            'Lightings' => 'lightbulb', 'Lights' => 'lightbulb', 'Toilet' => 'toilet', 'Prayer Room' => 'mosque',
            'Mushola' => 'mosque', 'First Aid' => 'first-aid',
        ];

        if ($request->has('facilities')) {
            foreach ($request->facilities as $facName) {
                if (empty(trim($facName))) continue;
                $icon = 'check-circle';
                foreach ($iconMap as $key => $val) {
                    if (stripos($facName, $key) !== false) {
                        $icon = $val;
                        break;
                    }
                }
                $field->facilities()->create([
                    'name_facilities' => trim($facName),
                    'icon' => $icon,
                ]);
            }
        }

        if ($request->has('custom_facilities') && !empty($request->custom_facilities)) {
            $customFacs = array_map('trim', explode(',', $request->custom_facilities));
            foreach ($customFacs as $facName) {
                if (empty($facName)) continue;
                $icon = 'check-circle';
                foreach ($iconMap as $key => $val) {
                    if (stripos($facName, $key) !== false) {
                        $icon = $val;
                        break;
                    }
                }
                $field->facilities()->create([
                    'name_facilities' => $facName,
                    'icon' => $icon,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Field added successfully.', 'field' => $field]);
    }

    public function apiEditField($id)
    {
        $field = Field::query()->with('facilities')->findOrFail($id);
        return response()->json($field);
    }

    public function apiUpdateField(Request $request, $id)
    {
        $field = Field::query()->findOrFail($id);

        $request->validate([
            'name_fields' => 'required|string|max:255',
            'type_fields' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_per_hour' => 'required|integer|min:0',
            'capacity' => 'required|integer|min:1',
            'sub_courts' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $subCourts = array_map('trim', explode(',', $request->sub_courts));
        $subCourts = array_filter($subCourts);
        if (empty($subCourts)) {
            return response()->json(['success' => false, 'message' => 'Sub-courts must be filled in a comma-separated format.'], 422);
        }

        $data = [
            'name_fields' => $request->name_fields,
            'type_fields' => $request->type_fields,
            'address' => $request->address,
            'description' => $request->description ?? '',
            'price_per_hour' => $request->price_per_hour,
            'capacity' => $request->capacity,
            'sub_courts' => $subCourts,
        ];

        if ($request->hasFile('image')) {
            if ($field->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($field->image);
            }
            $data['image'] = $request->file('image')->store('fields', 'public');
        }

        $field->update($data);

        // Sync facilities
        $field->facilities()->delete();

        $iconMap = [
            'Free WiFi' => 'wifi', 'WiFi' => 'wifi', 'Shower Room' => 'shower', 'Shower' => 'shower',
            'Secure Parking' => 'parking', 'Parking' => 'parking', 'Full AC Area' => 'snowflake', 'AC' => 'snowflake',
            'Locker Room' => 'key', 'Locker' => 'key', 'Canteen' => 'coffee', 'Cafe' => 'coffee',
            'Lightings' => 'lightbulb', 'Lights' => 'lightbulb', 'Toilet' => 'toilet', 'Prayer Room' => 'mosque',
            'Mushola' => 'mosque', 'First Aid' => 'first-aid',
        ];

        if ($request->has('facilities')) {
            foreach ($request->facilities as $facName) {
                if (empty(trim($facName))) continue;
                $icon = 'check-circle';
                foreach ($iconMap as $key => $val) {
                    if (stripos($facName, $key) !== false) {
                        $icon = $val;
                        break;
                    }
                }
                $field->facilities()->create([
                    'name_facilities' => trim($facName),
                    'icon' => $icon,
                ]);
            }
        }

        if ($request->has('custom_facilities') && !empty($request->custom_facilities)) {
            $customFacs = array_map('trim', explode(',', $request->custom_facilities));
            foreach ($customFacs as $facName) {
                if (empty($facName)) continue;
                $icon = 'check-circle';
                foreach ($iconMap as $key => $val) {
                    if (stripos($facName, $key) !== false) {
                        $icon = $val;
                        break;
                    }
                }
                $field->facilities()->create([
                    'name_facilities' => $facName,
                    'icon' => $icon,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Field updated successfully.', 'field' => $field]);
    }

    public function apiDestroyField($id)
    {
        $field = Field::query()->findOrFail($id);
        if ($field->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($field->image);
        }
        $field->facilities()->delete();
        $field->delete();
        return response()->json(['success' => true, 'message' => 'Field deleted successfully.']);
    }

    public function apiUsers()
    {
        $users = User::query()->orderBy('created_at', 'desc')->get();
        return response()->json([
            'users' => $users,
            'totalUsers' => User::query()->count(),
            'activeNow' => User::query()->where('created_at', '>=', now()->subDays(7))->count(),
            'banned' => User::query()->whereNotNull('banned_at')->count(),
        ]);
    }

    public function apiEditUser($id)
    {
        $user = User::query()->findOrFail($id);
        return response()->json($user);
    }

    public function apiUpdateUser(Request $request, $id)
    {
        $user = User::query()->findOrFail($id);
        $request->validate([
            'name_users' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id . ',id_users',
            'role' => 'required|string|in:user,admin',
        ]);
        $user->update($request->only('name_users', 'email', 'role'));
        return response()->json(['success' => true, 'message' => 'User updated successfully.', 'user' => $user]);
    }

    public function apiToggleBanUser($id)
    {
        $user = User::query()->findOrFail($id);
        if ($user->id_users === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'You cannot ban yourself.'], 403);
        }
        if ($user->banned_at) {
            $user->update(['banned_at' => null]);
            $message = 'User unbanned successfully.';
        } else {
            $user->update(['banned_at' => now()]);
            $message = 'User banned successfully.';
        }
        return response()->json(['success' => true, 'message' => $message, 'user' => $user]);
    }

    public function apiBookings()
    {
        $bookings = Booking::query()->with(['user', 'schedule.field', 'payment'])->latest()->get();
        return response()->json($bookings);
    }

    public function apiBookingDetail($id)
    {
        $booking = Booking::query()->with(['user', 'schedule.field', 'payment'])->findOrFail($id);
        return response()->json($booking);
    }

    public function apiBookingInvoice($id)
    {
        $booking = Booking::query()->with(['user', 'schedule.field', 'payment'])->findOrFail($id);
        return response()->json($booking);
    }

    public function apiForcePaid($id)
    {
        $booking = Booking::query()->findOrFail($id);
        $booking->update(['status_bookings' => 'Paid']);
        Payment::where('booking_id', $id)->update([
            'status_payments' => 'settlement',
            'paid_at' => now()
        ]);
        return response()->json(['success' => true, 'message' => 'Booking forced to Paid.', 'booking' => $booking]);
    }

    public function apiCancelBooking($id)
    {
        $booking = Booking::query()->with('schedule.field')->findOrFail($id);
        if ($booking->status_bookings === 'Cancelled') {
            return response()->json(['success' => false, 'message' => 'This booking has already been cancelled.'], 422);
        }
        $booking->update([
            'status_bookings' => 'Cancelled',
            'cancelled_at' => now(),
            'cancel_reason' => 'Cancelled by admin',
        ]);
        $this->freeSchedules($booking);
        return response()->json(['success' => true, 'message' => 'Booking cancelled.', 'booking' => $booking]);
    }
}
