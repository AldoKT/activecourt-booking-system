<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function forcePaid($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = $this->getToken();

        $response = Http::withToken($token)->post($backendUrl . '/payment/force-paid/' . $id);

        if ($response->successful()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to update payment status.'], 500);
    }

    public function verify($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $response = Http::withToken($this->getToken())->post($backendUrl . '/payment/verify/' . $id);

        return response()->json($response->json(), $response->status());
    }

    /**
     * Get token from session
     */
    private function getToken()
    {
        return session('api_token');
    }
}
