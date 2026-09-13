@extends('layouts.app')

@section('title', 'About Us - ActiveCourt')

@section('content')
<div class="bg-slate-50 min-h-screen relative overflow-hidden font-sans">
    <!-- Decorative background elements similar to Fields page -->
    <div class="absolute top-[-10%] left-[-5%] w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse pointer-events-none z-0"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse pointer-events-none z-0" style="animation-delay: 2s;"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-40 pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-24 space-y-24">
        
        <!-- Hero Section -->
        <section class="flex flex-col lg:flex-row items-center gap-16" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
            <div class="flex-1 space-y-6 transition-all duration-1000 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <h1 class="text-5xl lg:text-6xl font-extrabold text-slate-800 leading-tight">
                    About <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-400">ActiveCourt</span>
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed max-w-lg">
                    Making sports facility booking simple, fast, and accessible for everyone.
                </p>
                <div class="pt-4 flex gap-4">
                    <a href="{{ route('fields.index') }}" class="bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg hover:shadow-primary-500/30 hover:-translate-y-1">
                        Browse Fields
                    </a>
                </div>
            </div>
            <div class="flex-1 w-full transition-all duration-1000 delay-300 transform" :class="show ? 'translate-x-0 opacity-100' : 'translate-x-12 opacity-0'">
                <div class="relative w-full h-[500px] rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                    <img src="https://images.unsplash.com/photo-1544919982-b61976f0ba43?auto=format&fit=crop&q=80&w=1200" alt="Sports People" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 to-transparent"></div>
                </div>
            </div>
        </section>

        <!-- Second Section (Features) -->
        <section x-data="{ show: false }" x-init="setTimeout(() => show = true, 300)">
            <div class="text-center mb-16 transition-all duration-700 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <h2 class="text-3xl font-bold text-slate-800">Why Choose Us?</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">We prioritize your convenience, safety, and satisfaction.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group transition-all duration-700 delay-100 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                    <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary-500 transition-colors duration-300 shadow-sm">
                        <i class="fas fa-mouse-pointer text-primary-600 text-2xl group-hover:text-white transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Easy Booking</h3>
                    <p class="text-slate-600 leading-relaxed">Book your favorite sports venue in just a few clicks.</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group transition-all duration-700 delay-200 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                    <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary-500 transition-colors duration-300 shadow-sm">
                        <i class="fas fa-check-circle text-primary-600 text-2xl group-hover:text-white transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Trusted Venues</h3>
                    <p class="text-slate-600 leading-relaxed">Verified sports facilities with accurate information.</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group transition-all duration-700 delay-300 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                    <div class="w-14 h-14 bg-primary-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-primary-500 transition-colors duration-300 shadow-sm">
                        <i class="fas fa-shield-alt text-primary-600 text-2xl group-hover:text-white transition-colors"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Secure Payment</h3>
                    <p class="text-slate-600 leading-relaxed">Fast and secure online payment system.</p>
                </div>
            </div>
        </section>

        <!-- Third Section (Statistics) -->
        <section x-data="{ show: false }" x-init="setTimeout(() => show = true, 500)">
            <div class="bg-white/70 backdrop-blur-xl rounded-[2.5rem] p-12 border border-white/60 lg:p-16 relative overflow-hidden shadow-2xl transition-all duration-1000 transform" :class="show ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 pointer-events-none"></div>
                
                <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-8">
                    <div class="text-center group bg-white/50 backdrop-blur-sm rounded-3xl p-6 shadow-sm hover:shadow-md transition duration-300">
                        <div class="text-5xl font-extrabold text-primary-600 mb-2 group-hover:scale-110 transition-transform duration-300">500+</div>
                        <div class="text-slate-600 font-semibold tracking-wide">Sports Fields</div>
                    </div>
                    <div class="text-center group bg-white/50 backdrop-blur-sm rounded-3xl p-6 shadow-sm hover:shadow-md transition duration-300">
                        <div class="text-5xl font-extrabold text-primary-600 mb-2 group-hover:scale-110 transition-transform duration-300">20</div>
                        <div class="text-slate-600 font-semibold tracking-wide">Cities</div>
                    </div>
                    <div class="text-center group bg-white/50 backdrop-blur-sm rounded-3xl p-6 shadow-sm hover:shadow-md transition duration-300">
                        <div class="text-5xl font-extrabold text-primary-600 mb-2 group-hover:scale-110 transition-transform duration-300">50k+</div>
                        <div class="text-slate-600 font-semibold tracking-wide">Users</div>
                    </div>
                    <div class="text-center group bg-white/50 backdrop-blur-sm rounded-3xl p-6 shadow-sm hover:shadow-md transition duration-300">
                        <div class="text-5xl font-extrabold text-primary-600 mb-2 group-hover:scale-110 transition-transform duration-300">98%</div>
                        <div class="text-slate-600 font-semibold tracking-wide">Satisfaction</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fourth Section (Mission & Vision) -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-8" x-data="{ show: false }" x-init="setTimeout(() => show = true, 700)">
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-10 lg:p-14 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 flex flex-col justify-center transition-all duration-700 transform" :class="show ? 'translate-x-0 opacity-100' : '-translate-x-12 opacity-0'">
                <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                    <i class="fas fa-rocket text-primary-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Our Mission</h3>
                <p class="text-lg text-slate-600 leading-relaxed font-medium">"Our mission is to simplify sports venue reservations through modern technology."</p>
            </div>
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-10 lg:p-14 shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 flex flex-col justify-center transition-all duration-700 delay-200 transform" :class="show ? 'translate-x-0 opacity-100' : 'translate-x-12 opacity-0'">
                <div class="w-16 h-16 bg-accent-100 rounded-2xl flex items-center justify-center mb-6 shadow-sm">
                    <i class="fas fa-eye text-accent-600 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 mb-4">Our Vision</h3>
                <p class="text-lg text-slate-600 leading-relaxed font-medium">"Become Indonesia's leading sports booking platform."</p>
            </div>
        </section>

        <!-- Fifth Section (How It Works) -->
        <section x-data="{ show: false }" x-init="setTimeout(() => show = true, 900)">
            <div class="text-center mb-16 transition-all duration-700 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <h2 class="text-3xl font-bold text-slate-800">How It Works</h2>
                <p class="text-slate-500 mt-3 max-w-2xl mx-auto">Book your favorite sports venue in 3 simple steps.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <!-- Connecting Line (Desktop Only) -->
                <div class="hidden md:block absolute top-1/2 left-1/6 right-1/6 h-0.5 bg-gradient-to-r from-primary-200 via-accent-200 to-green-200 -translate-y-1/2 z-0"></div>

                <!-- Step 1 -->
                <div class="relative z-10 bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 p-8 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group transition-all duration-700 delay-100 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                    <div class="w-20 h-20 mx-auto bg-primary-100 rounded-full flex items-center justify-center mb-6 border-4 border-white shadow-md group-hover:bg-primary-500 transition-colors duration-300 relative">
                        <i class="fas fa-search text-primary-600 text-2xl group-hover:text-white transition-colors"></i>
                        <span class="absolute -top-2 -right-2 bg-slate-800 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold border-2 border-white shadow-sm">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Find a Field</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Search for sports fields near you based on location, type of sport, and availability.</p>
                </div>
                
                <!-- Step 2 -->
                <div class="relative z-10 bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 p-8 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group transition-all duration-700 delay-200 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                    <div class="w-20 h-20 mx-auto bg-accent-100 rounded-full flex items-center justify-center mb-6 border-4 border-white shadow-md group-hover:bg-accent-500 transition-colors duration-300 relative">
                        <i class="fas fa-calendar-check text-accent-600 text-2xl group-hover:text-white transition-colors"></i>
                        <span class="absolute -top-2 -right-2 bg-slate-800 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold border-2 border-white shadow-sm">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Book & Pay</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Choose your preferred schedule and complete the payment securely online.</p>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 bg-white/80 backdrop-blur-xl rounded-3xl border border-white/60 p-8 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group transition-all duration-700 delay-300 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                    <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-6 border-4 border-white shadow-md group-hover:bg-green-500 transition-colors duration-300 relative">
                        <i class="fas fa-running text-green-600 text-2xl group-hover:text-white transition-colors"></i>
                        <span class="absolute -top-2 -right-2 bg-slate-800 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold border-2 border-white shadow-sm">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Time to Play</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Show up at the venue with your confirmation code and enjoy the game!</p>
                </div>
            </div>
        </section>

        <!-- Bottom CTA -->
        <section x-data="{ show: false }" x-init="setTimeout(() => show = true, 1100)">
            <div class="bg-gradient-to-br from-white/80 to-white/40 backdrop-blur-2xl rounded-3xl border border-white shadow-2xl p-16 text-center max-w-4xl mx-auto transition-all duration-1000 transform" :class="show ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
                <h2 class="text-4xl font-extrabold text-slate-800 mb-4">Ready to book your next game?</h2>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
                    <a href="{{ route('fields.index') }}" class="bg-gradient-to-r from-primary-600 to-primary-500 hover:from-primary-700 hover:to-primary-600 text-white font-bold py-4 px-10 rounded-2xl transition shadow-xl hover:shadow-primary-500/30 hover:-translate-y-1 text-lg">
                        Browse Fields
                    </a>
                    <a href="{{ route('contact') }}" class="bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white font-bold py-4 px-10 rounded-2xl transition shadow-xl hover:shadow-accent-500/30 hover:-translate-y-1 text-lg">
                        Contact Us
                    </a>
                </div>
            </div>
        </section>

    </div>
</div>

@endsection