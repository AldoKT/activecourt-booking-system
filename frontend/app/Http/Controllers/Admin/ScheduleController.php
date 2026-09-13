<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->get($backendUrl . '/admin/schedules', $request->all());

        if (!$response->successful()) {
            return redirect()->route('home')->with('error', 'Failed to retrieve schedules from backend.');
        }

        $data = json_decode(json_encode($response->json()));
        $fields = $data->fields ?? [];
        $schedules = $data->schedules ?? [];

        return view('admin.schedules', compact('fields', 'schedules'));
    }

    public function store(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/admin/schedules', $request->all());

        if ($response->successful()) {
            $msg = $response->json('message') ?? 'Schedules generated.';
            return redirect()->route('admin.schedules')->with('success', $msg);
        }

        $error = $response->json('message') ?? 'Failed to generate schedules.';
        return back()->with('error', $error)->withInput();
    }

    public function update(Request $request, $id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->put($backendUrl . '/admin/schedules/' . $id, $request->all());

        if ($response->successful()) {
            return redirect()->route('admin.schedules')->with('success', 'Schedule updated successfully.');
        }

        $error = $response->json('message') ?? 'Failed to update schedule.';
        return back()->with('error', $error)->withInput();
    }

    public function destroy($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->delete($backendUrl . '/admin/schedules/' . $id);

        if ($response->successful()) {
            return redirect()->route('admin.schedules')->with('success', 'Schedule deleted successfully.');
        }

        $error = $response->json('message') ?? 'Failed to delete schedule.';
        return back()->with('error', $error);
    }

    public function toggleStatus($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/admin/schedules/' . $id . '/toggle');

        if ($response->successful()) {
            $msg = $response->json('message') ?? 'Schedule status changed.';
            return redirect()->route('admin.schedules')->with('success', $msg);
        }

        $error = $response->json('message') ?? 'Failed to change schedule status.';
        return back()->with('error', $error);
    }

    public function destroyAll(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/admin/schedules/destroy-all', $request->all());

        if ($response->successful()) {
            $msg = $response->json('message') ?? 'Schedules deleted.';
            return redirect()->route('admin.schedules')->with('success', $msg);
        }

        return back()->with('error', 'Failed to delete schedules.');
    }

    /**
     * Get token from session
     */
    private function getToken()
    {
        return session('api_token');
    }
}
