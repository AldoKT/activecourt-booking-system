<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/dashboard');

        if (!$response->successful()) {
            return redirect()->route('home')->with('error', 'Unauthorized or failed to connect to backend.');
        }

        $data = json_decode(json_encode($response->json()));

        return view('admin.dashboard', [
            'totalRevenue' => $data->totalRevenue,
            'activeBookings' => $data->activeBookings,
            'newUsers' => $data->newUsers,
            'conversionRate' => $data->conversionRate,
            'recentBookings' => $data->recentBookings,
            'totalFields' => $data->totalFields,
            'totalUsers' => $data->totalUsers,
            'todayBookings' => $data->todayBookings,
            'todayRevenue' => $data->todayRevenue,
            'pendingBookings' => $data->pendingBookings,
        ]);
    }

    // ==================== FIELDS ====================

    public function fields()
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/fields');
        $fields = json_decode(json_encode($response->json() ?? []));

        return view('admin.fields', compact('fields'));
    }

    public function storeField(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $requestHttp = Http::withToken($token);

        if ($request->hasFile('image')) {
            $requestHttp->attach(
                'image',
                file_get_contents($request->file('image')->getRealPath()),
                $request->file('image')->getClientOriginalName()
            );
        }

        if ($request->has('facilities')) {
            foreach ($request->facilities as $fac) {
                $requestHttp->attach('facilities[]', $fac);
            }
        }

        $response = $requestHttp->post($backendUrl . '/admin/fields', $request->except(['image', 'facilities']));

        if ($response->successful()) {
            return redirect()->route('admin.fields')->with('success', 'Field added successfully.');
        }

        $error = $response->json('message') ?? 'Failed to add field.';
        return back()->with('error', $error)->withInput();
    }

    public function editField($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/fields/' . $id . '/edit');
        if (!$response->successful()) {
            abort(404);
        }

        $field = json_decode(json_encode($response->json()));

        return view('admin.field-edit', compact('field'));
    }

    public function updateField(Request $request, $id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $requestHttp = Http::withToken($token);

        if ($request->hasFile('image')) {
            $requestHttp->attach(
                'image',
                file_get_contents($request->file('image')->getRealPath()),
                $request->file('image')->getClientOriginalName()
            );
        }

        if ($request->has('facilities')) {
            foreach ($request->facilities as $fac) {
                $requestHttp->attach('facilities[]', $fac);
            }
        }

        $response = $requestHttp->post($backendUrl . '/admin/fields/' . $id, $request->except(['image', 'facilities']));

        if ($response->successful()) {
            return redirect()->route('admin.fields')->with('success', 'Field updated successfully.');
        }

        $error = $response->json('message') ?? 'Failed to update field.';
        return back()->with('error', $error)->withInput();
    }

    public function destroyField($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->delete($backendUrl . '/admin/fields/' . $id);

        if ($response->successful()) {
            return redirect()->route('admin.fields')->with('success', 'Field deleted successfully.');
        }

        return back()->with('error', 'Failed to delete field.');
    }

    // ==================== USERS ====================

    public function users()
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/users');
        $data = json_decode(json_encode($response->json()));

        return view('admin.users', [
            'users' => $data->users ?? [],
            'totalUsers' => $data->totalUsers ?? 0,
            'activeNow' => $data->activeNow ?? 0,
            'banned' => $data->banned ?? 0,
        ]);
    }

    public function editUser($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/users/' . $id . '/edit');
        if (!$response->successful()) {
            abort(404);
        }

        $user = json_decode(json_encode($response->json()));

        return view('admin.user-edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->put($backendUrl . '/admin/users/' . $id, $request->all());

        if ($response->successful()) {
            return redirect()->route('admin.users')->with('success', 'User updated successfully.');
        }

        $error = $response->json('message') ?? 'Failed to update user.';
        return back()->with('error', $error)->withInput();
    }

    public function toggleBanUser($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/admin/users/' . $id . '/ban');

        if ($response->successful()) {
            return redirect()->route('admin.users')->with('success', $response->json('message'));
        }

        $error = $response->json('message') ?? 'Failed to ban/unban user.';
        return back()->with('error', $error);
    }

    // ==================== BOOKINGS ====================

    public function bookings()
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/bookings');
        $bookings = json_decode(json_encode($response->json() ?? []));

        return view('admin.bookings', compact('bookings'));
    }

    public function bookingDetail($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/bookings/' . $id);
        if (!$response->successful()) {
            abort(404);
        }

        $booking = $this->hydrateBooking($response->json());

        return view('admin.booking-detail', compact('booking'));
    }

    public function bookingInvoice($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/bookings/' . $id . '/invoice');
        if (!$response->successful()) {
            abort(404);
        }

        $booking = $this->hydrateBooking($response->json());

        return view('user.invoice', compact('booking'));
    }

    public function forcePaid($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/admin/bookings/' . $id . '/force-paid');

        if ($response->successful()) {
            return redirect()->route('admin.bookings')->with('success', $response->json('message'));
        }

        $error = $response->json('message') ?? 'Failed to force booking to paid.';
        return back()->with('error', $error);
    }

    public function cancelBooking($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/admin/bookings/' . $id . '/cancel');

        if ($response->successful()) {
            return redirect()->route('admin.bookings')->with('success', $response->json('message'));
        }

        $error = $response->json('message') ?? 'Failed to cancel booking.';
        return back()->with('error', $error);
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

    /**
     * Get token from session
     */
    private function getToken()
    {
        return session('api_token');
    }
}
