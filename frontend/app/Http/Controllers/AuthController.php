<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class AuthController extends Controller
{
    public function loginSubmit(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');

        $response = Http::post($backendUrl . '/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['token'];
            $apiUser = $data['user'];

            // Save api_token and user to frontend session
            session([
                'api_token' => $token,
                'api_user' => $apiUser
            ]);

            // Sync/Login locally using the matching database user ID
            Auth::loginUsingId($apiUser['id_users']);

            if ($apiUser['role'] === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Logged in as Admin: ' . $apiUser['username']);
            }
            
            return redirect()->route('home')->with('success', 'Welcome back, ' . $apiUser['username'] . '!');
        }

        return redirect()->route('login')->with('error', 'email atau password salah');
    }

    public function registerSubmit(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');

        // Post registration to backend
        $response = Http::post($backendUrl . '/register', [
            'name_users' => $request->name_users,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $token = $data['token'];
            $apiUser = $data['user'];

            session([
                'api_token' => $token,
                'api_user' => $apiUser
            ]);

            Auth::loginUsingId($apiUser['id_users']);

            return redirect()->route('home')->with('success', 'Registration successful!');
        }

        // Return with error bag from backend
        $errors = $response->json()['errors'] ?? ['registration' => ['Registration failed.']];
        return back()->withErrors($errors)->withInput();
    }

    public function forgotPassword()
    {
        return redirect()->route('login')->with('success', 'Reset password link has been sent to your email.');
    }

    public function googleAuth()
    {
        // Redirect directly to the Backend Google Auth route
        $backendPublicUrl = env('BACKEND_PUBLIC_URL', 'http://localhost:8001');
        return redirect()->away($backendPublicUrl . '/auth/google');
    }

    public function googleCallbackForward(Request $request)
    {
        $backendPublicUrl = env('BACKEND_PUBLIC_URL', 'http://localhost:8001');
        
        $query = $request->getQueryString();
        return redirect()->away($backendPublicUrl . '/auth/google/callback?' . $query);
    }

    public function googleSuccess(Request $request)
    {
        $token = $request->query('token');
        $userId = $request->query('user_id');

        if (!$token || !$userId) {
            return redirect()->route('login')->with('error', 'Google login failed.');
        }

        // Authenticate locally using the database user ID
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User record not found.');
        }

        session([
            'api_token' => $token,
            'api_user' => $user->toArray()
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Login Google berhasil!');
    }

    public function logout(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        $token = session('api_token');

        if ($token) {
            Http::withToken($token)->post($backendUrl . '/logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}