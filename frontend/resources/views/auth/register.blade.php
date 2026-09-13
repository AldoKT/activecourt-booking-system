@extends('layouts.app')

@section('title', 'Sign Up - ActiveCourt')

@section('content')
<!-- Tombol Back -->
<a href="{{ url()->previous() }}" class="absolute top-6 left-6 z-50 flex items-center gap-2 px-4 py-2 rounded-lg backdrop-blur-md transition shadow-sm border font-semibold text-sm text-slate-700 bg-slate-100 border-slate-200 hover:bg-slate-200 lg:text-white lg:bg-white/20 lg:border-white/20 lg:hover:bg-white/30">
    <i class="fas fa-arrow-left"></i> Back
</a>

<div class="flex min-h-screen">
    <div class="hidden lg:flex lg:w-1/2 relative bg-slate-900 items-end p-12">
        <img src="https://images.unsplash.com/photo-1545224876-13e721b017b2?auto=format&fit=crop&q=80&w=2000" 
             alt="Sports Court" 
             class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-overlay">
        <div class="relative z-10 max-w-lg">
            <h2 class="text-4xl font-bold text-white mb-4 leading-tight">Play More. Book Easier.</h2>
            <p class="text-slate-300 text-lg">Join thousands of players who book sports facilities through ActiveCourt.</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">
            <div class="mb-8">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-2xl font-bold text-primary-700 mb-6">
                    <i class="fas fa-layer-group text-primary-600"></i> ActiveCourt
                </a>
                <h1 class="text-2xl font-bold text-slate-800">Create Your Account</h1>
                <p class="text-slate-500 text-sm mt-1">Create an account and start booking your favorite sports venues.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="name_users" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="far fa-user"></i>
                        </span>
                        <input id="name_users" type="text" name="name_users" value="{{ old('name_users') }}" required autofocus
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                            placeholder="John Doe">
                    </div>
                    @error('name_users') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="username" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-at"></i>
                        </span>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                            placeholder="johndoe123">
                    </div>
                    @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="far fa-envelope"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                            placeholder="you@example.com">
                    </div>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Phone Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                            placeholder="081234567890">
                    </div>
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Password</label>
                        <div class="relative" x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                class="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600 transition focus:outline-none">
                                <i :class="show ? 'fas fa-eye-slash text-xs' : 'fas fa-eye text-xs'"></i>
                            </button>
                        </div>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">Confirm Password</label>
                        <div class="relative" x-data="{ show: false }">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                                class="w-full pl-10 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm transition"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 hover:text-slate-600 transition focus:outline-none">
                                <i :class="show ? 'fas fa-eye-slash text-xs' : 'fas fa-eye text-xs'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-lg transition mt-2">
                    Create Account
                </button>
            </form>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center text-xs"><span class="bg-white px-4 text-slate-400">or continue with</span></div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-2 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold py-2 rounded-lg transition text-xs">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-4 h-4" alt="Google">
                    Google
                </a>
                <button type="button" class="w-full flex items-center justify-center gap-2 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold py-2 rounded-lg transition text-xs">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.09 2.31-.86 3.5-.8 1.49.07 2.65.65 3.3 1.5-3.03 1.83-2.58 5.92.51 7.1-1.06 2.76-2.53 5.34-2.39 4.37zm-3.41-14.73c.18-2.66 2.18-4.57 4.54-4.55-.44 2.86-2.88 4.79-4.54 4.55z"/>
                    </svg>
                    Apple
                </button>
            </div>

            <p class="text-center text-sm text-slate-500 mt-6">
                Already have an account? <a href="{{ route('login') }}" class="font-bold text-primary-600 hover:text-primary-700">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
