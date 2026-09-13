@extends('layouts.app')

@section('title', 'ActiveCourt - Secure Your Game')

@section('content')
<!-- Hero Section (Minimum 1 Screen) -->
<section class="relative bg-primary-950 text-white min-h-screen flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/hero-bg.jpg') }}" 
             alt="Background" 
             class="w-full h-full object-cover opacity-60" />
             
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-primary-950 via-transparent to-primary-950/40"></div>
        
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-accent-500/20 rounded-full filter blur-[100px] opacity-60 animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/20 rounded-full filter blur-[100px] opacity-60 animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center z-10">
        <span class="inline-block py-1.5 px-4 rounded-full bg-white/10 border border-white/20 backdrop-blur-md text-sm font-semibold tracking-wider text-accent-300 mb-6 uppercase">
            Premium Sports Facilities
        </span>
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 leading-tight text-transparent bg-clip-text bg-gradient-to-br from-white via-slate-200 to-slate-500 drop-shadow-sm">
            Secure Your Game.<br><span class="text-white">Own The Court.</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-300 mb-12 max-w-3xl mx-auto font-light leading-relaxed">
            The professional standard for finding and booking premium sports facilities. Instantly secure futsal, basketball, and badminton courts with real-time availability.
        </p>

        <form action="{{ route('fields.index') }}" method="GET" class="bg-white/10 backdrop-blur-xl border border-white/20 p-2.5 rounded-2xl flex flex-col md:flex-row gap-3 max-w-4xl mx-auto shadow-2xl transition-all duration-300 hover:bg-white/15">
            <div class="flex-1 relative flex items-center bg-white rounded-xl">
                <i class="fas fa-map-marker-alt text-slate-400 absolute left-5"></i>
                <input type="text" name="location" placeholder="Location or Facility Name" class="w-full pl-12 pr-4 py-4 bg-transparent border-none text-slate-800 font-medium focus:ring-0 placeholder-slate-400 rounded-xl outline-none">
            </div>
            <div class="flex-1 relative flex items-center bg-white rounded-xl">
                <i class="far fa-futbol text-slate-400 absolute left-5"></i>
                <select name="sports" class="w-full pl-12 pr-4 py-4 bg-transparent border-none text-slate-600 font-medium focus:ring-0 appearance-none rounded-xl cursor-pointer outline-none">
                    <option value="">Select Sport</option>
                    <option value="Futsal">Futsal</option>
                    <option value="Badminton">Badminton</option>
                    <option value="Basket">Basketball</option>
                </select>
                <i class="fas fa-chevron-down text-slate-400 absolute right-5 text-sm pointer-events-none"></i>
            </div>
            <button type="submit" class="bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white font-bold px-10 py-4 rounded-xl transition shadow-lg hover:shadow-accent-500/25 flex items-center justify-center gap-2 whitespace-nowrap text-lg">
                <i class="fas fa-search"></i> Book Now
            </button>
        </form>
    </div>
</section>

<!-- Choose Arena Section (Minimum 1 Screen) -->
<section class="min-h-screen flex flex-col justify-center py-20 bg-slate-50 relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-primary-100 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-96 h-96 bg-accent-100 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="text-center mb-16">
            <h2 class="text-sm text-accent-600 font-bold tracking-[0.2em] uppercase mb-3">Explore Facilities</h2>
            <h3 class="text-4xl md:text-5xl font-extrabold text-slate-900">Choose Your Arena</h3>
            <div class="mt-6 w-24 h-1.5 bg-accent-500 mx-auto rounded-full"></div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @if($sports->count() > 0)
                @php $first = $sports->first(); @endphp
                <!-- Large Card -->
                <a href="{{ route('fields.index', ['sports' => $first->type_fields]) }}" class="group relative rounded-3xl overflow-hidden md:col-span-2 aspect-[2/1] lg:aspect-auto lg:h-full min-h-[400px] shadow-lg hover:shadow-2xl transition-all duration-500 block">
                    <img src="{{ $first->image ? asset('storage/' . $first->image) : 'https://images.unsplash.com/photo-1524015368236-bbf6f72545b6?auto=format&fit=crop&q=80&w=1000' }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-in-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="absolute top-6 right-6 bg-white/20 backdrop-blur-md rounded-full px-4 py-2 border border-white/30 z-10">
                        <span class="text-white font-semibold text-sm flex items-center gap-2"><i class="fas fa-fire text-accent-400"></i> Popular</span>
                    </div>

                    <div class="absolute bottom-8 left-8 right-8 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500 z-10">
                        <div class="w-12 h-12 bg-accent-500 text-white rounded-2xl flex items-center justify-center text-xl mb-4 shadow-lg group-hover:-translate-y-2 transition-transform duration-500">
                            @if(strtolower($first->type_fields) == 'futsal') <i class="far fa-futbol"></i>
                            @elseif(strtolower($first->type_fields) == 'basket' || strtolower($first->type_fields) == 'basketball') <i class="fas fa-basketball-ball"></i>
                            @elseif(strtolower($first->type_fields) == 'badminton') <i class="fas fa-volleyball-ball"></i>
                            @else <i class="fas fa-running"></i> @endif
                        </div>
                        <h3 class="text-3xl font-bold text-white mb-2">{{ $first->type_fields }}</h3>
                        <p class="text-slate-300 text-base mb-4 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">{{ Str::limit($first->description ?? 'Experience pro-level turf and lighting. Perfect for your matches.', 100) }}</p>
                        <span class="inline-flex items-center text-accent-400 font-bold text-sm group-hover:text-accent-300">
                            Explore {{ $first->count }} Courts <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                        </span>
                    </div>
                </a>
            @endif
            
            @if($sports->count() > 1)
            <div class="flex flex-col gap-8 h-full">
                @foreach($sports->skip(1) as $sport)
                    <!-- Small Card -->
                    <a href="{{ route('fields.index', ['sports' => $sport->type_fields]) }}" class="group relative rounded-3xl overflow-hidden flex-1 shadow-lg hover:shadow-2xl transition-all duration-500 block min-h-[250px]">
                        <img src="{{ $sport->image ? asset('storage/' . $sport->image) : 'https://images.unsplash.com/photo-1504450758481-7338eba7524a?auto=format&fit=crop&q=80&w=800' }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-in-out">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <div class="absolute bottom-6 left-6 right-6 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-500 z-10">
                            <h3 class="text-2xl font-bold text-white mb-1">{{ $sport->type_fields }}</h3>
                            <p class="text-slate-300 text-sm mb-3">{{ Str::limit($sport->description ?? 'Premium courts for your game.', 50) }}</p>
                            <span class="inline-flex items-center text-accent-400 font-bold text-xs">
                                {{ $sport->count }} Courts <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</section>

<!-- Why Choose Us Section (Minimum 1 Screen) -->
<section class="min-h-screen flex flex-col justify-center py-20 bg-white relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-1/2 left-0 w-72 h-72 bg-accent-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 -translate-x-1/2 pointer-events-none"></div>
    <div class="absolute top-1/2 right-0 w-72 h-72 bg-primary-50 rounded-full blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="text-center mb-16">
            <h2 class="text-sm text-accent-600 font-bold tracking-[0.2em] uppercase mb-3">Our Excellence</h2>
            <h3 class="text-4xl md:text-5xl font-extrabold text-slate-900">Why Choose ActiveCourt</h3>
            <div class="mt-6 w-24 h-1.5 bg-accent-500 mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:border-accent-200 hover:shadow-xl transition-all duration-300 group text-center">
                <div class="w-16 h-16 mx-auto bg-white text-accent-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 group-hover:bg-accent-500 group-hover:text-white transition-all duration-300">
                    <i class="fas fa-bolt"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-3">Real-time Booking</h4>
                <p class="text-slate-600 text-sm leading-relaxed">No more waiting. Check availability and secure your court instantly with our live scheduling system.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:border-primary-200 hover:shadow-xl transition-all duration-300 group text-center">
                <div class="w-16 h-16 mx-auto bg-white text-primary-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 group-hover:bg-primary-500 group-hover:text-white transition-all duration-300">
                    <i class="fas fa-medal"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-3">Premium Quality</h4>
                <p class="text-slate-600 text-sm leading-relaxed">We only partner with top-tier facilities ensuring professional-grade surfaces and excellent lighting.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:border-green-200 hover:shadow-xl transition-all duration-300 group text-center">
                <div class="w-16 h-16 mx-auto bg-white text-green-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 group-hover:bg-green-500 group-hover:text-white transition-all duration-300">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-3">Secure Payments</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Your transactions are protected with industry-standard encryption for a safe booking experience.</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100 hover:border-purple-200 hover:shadow-xl transition-all duration-300 group text-center">
                <div class="w-16 h-16 mx-auto bg-white text-purple-500 rounded-2xl flex items-center justify-center text-2xl mb-6 shadow-sm group-hover:scale-110 group-hover:bg-purple-500 group-hover:text-white transition-all duration-300">
                    <i class="fas fa-headset"></i>
                </div>
                <h4 class="text-xl font-bold text-slate-800 mb-3">24/7 Support</h4>
                <p class="text-slate-600 text-sm leading-relaxed">Our dedicated customer service team is always ready to help you with any questions or issues.</p>
            </div>
        </div>
    </div>
</section>
@endsection