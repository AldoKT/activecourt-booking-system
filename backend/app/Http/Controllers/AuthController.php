<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginSubmit(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            
            // Redirect sesuai role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil masuk sebagai Admin: ' . $user->username);
            }
            
            return redirect()->route('home')->with('success', 'Selamat datang kembali, ' . $user->username . '!');
        }

        return redirect()->route('login')->with('error', 'email atau password salah');
    }

    public function registerSubmit(Request $request)
    {
        $request->validate([
            'name_users' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name_users' => $request->name_users,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => 'user',
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login.');
    }

    public function forgotPassword()
    {
        return redirect()->route('login')->with('success', 'Link reset password telah dikirim ke email prototipe Anda.');
    }

    public function googleAuth()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();

        $user = User::firstOrCreate(
            [
                'email' => $googleUser->email
            ],
            [
                'username' => explode('@', $googleUser->email)[0],
                'name_users' => $googleUser->name,
                'role' => 'user',
                'password' => bcrypt(Str::random(16))
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        // Redirect back to Frontend success page on port 8000
        $frontendSuccessUrl = 'http://localhost:8000/auth/google/success';

        return redirect()->away($frontendSuccessUrl . '?token=' . $token . '&user_id=' . $user->id_users);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function apiLogin(Request $request)
    {
        $user = User::query()->where('email', $request->email)->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'email atau password salah'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function apiRegister(Request $request)
    {
        $request->validate([
            'name_users' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name_users' => $request->name_users,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'role' => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user
        ], 201);
    }

    public function apiLogout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function apiLoginGoogle(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        $user = User::firstOrCreate(
            [
                'email' => $request->email
            ],
            [
                'username' => explode('@', $request->email)[0],
                'name_users' => $request->name,
                'role' => 'user',
                'password' => bcrypt(\Illuminate\Support\Str::random(16))
            ]
        );

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    }
}