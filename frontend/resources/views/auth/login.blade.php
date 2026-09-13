@extends('layouts.app')

@section('title', 'Login - ActiveCourt')

@section('content')
<!-- Tombol Back -->
<a href="{{ url()->previous() }}" class="absolute top-6 left-6 z-50 flex items-center gap-2 px-4 py-2 rounded-lg backdrop-blur-md transition shadow-sm border font-semibold text-sm text-slate-700 bg-slate-100 border-slate-200 hover:bg-slate-200 lg:text-white lg:bg-white/20 lg:border-white/20 lg:hover:bg-white/30">
    <i class="fas fa-arrow-left"></i> Back
</a>

<div class="flex min-h-screen">
    <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 items-end p-12">
        <img src="https://images.unsplash.com/photo-1504450758481-7338eba7524a?auto=format&fit=crop&q=80&w=2000" 
             alt="Stadium Lights" 
             class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-overlay">
        <div class="relative z-10 max-w-lg">
            <h2 class="text-4xl font-bold text-white mb-4 leading-tight">Manage your facilities with precision.</h2>
            <p class="text-slate-300 text-lg">ActiveCourt is the professional standard for scheduling, booking, and high-performance field management.</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            <div class="mb-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-2xl font-bold text-primary-700 mb-6">
                    <i class="fas fa-layer-group text-primary-600"></i> ActiveCourt
                </a>
                <h1 class="text-2xl font-bold text-slate-800">Welcome Back</h1>
                <p class="text-slate-500 text-sm mt-1">Enter your details to access your dashboard.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="far fa-envelope"></i>
                        </span>
                        <input type="email" name="email" required autofocus
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                            placeholder="you@example.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Password</label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input :type="show ? 'text' : 'password'" name="password" required
                            class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        Remember for 30 days
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-lg transition">
                    Sign In
                </button>
            </form>

            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center text-xs"><span class="bg-white px-4 text-slate-400">or continue with</span></div>
            </div>

            <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold py-2.5 rounded-lg transition text-sm">
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5" alt="Google">
                Sign in with Google
            </a>

            <p class="text-center text-sm text-slate-500 mt-8">
                Don't have an account? <a href="{{ route('register') }}" class="font-bold text-primary-600 hover:text-primary-700">Sign up</a>
            </p>
        </div>
    </div>
</div>
@endsection