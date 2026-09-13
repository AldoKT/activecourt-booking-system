@extends('layouts.app')

@section('title', 'Find Sports Venue - ActiveCourt')

@section('content')
    <div class="bg-slate-50 min-h-screen py-8 relative overflow-hidden">
        <!-- Decorative background elements -->
        <div class="absolute top-[-10%] left-[-5%] w-96 h-96 bg-primary-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse pointer-events-none z-0"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-accent-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-pulse pointer-events-none z-0" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 pointer-events-none z-0"></div>

        <div class="relative z-10 max-w-[1440px] mx-auto px-2 sm:px-3 lg:px-4">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-800">Find Sports Venue</h1>
                <p class="text-slate-500 mt-1">Find the best sports court in your city</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-4 lg:gap-6">
                <!-- Filter Panel - Sidebar -->
                <div class="w-full lg:w-72 flex-shrink-0" x-data="{ filtersOpen: false }">
                    <!-- Mobile Filter Toggle -->
                    <button @click="filtersOpen = !filtersOpen"
                        class="lg:hidden w-full flex items-center justify-between bg-white/80 backdrop-blur-md border border-white/50 shadow-sm rounded-xl px-4 py-3 mb-4">
                        <span class="font-semibold text-slate-700"><i class="fas fa-filter mr-2 text-accent-500"></i>Filters</span>
                        <i class="fas" :class="filtersOpen ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>

                    <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 shadow-xl p-6"
                        :class="filtersOpen ? 'block' : 'hidden lg:block'">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                                <i class="fas fa-sliders-h text-accent-500"></i>
                                Filters
                            </h3>
                            <a href="{{ route('fields.index') }}"
                                class="text-sm text-accent-600 hover:text-accent-700 font-medium">Clear Filters</a>
                        </div>

                        <form method="GET" action="{{ route('fields.index') }}" class="space-y-6" id="filter-form">
                            <!-- City / Location -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">City or area</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" name="location" value="{{ request('location') }}"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition shadow-inner outline-none"
                                        placeholder="Search city or area..."
                                        onchange="this.form.submit()">
                                </div>
                            </div>

                            <!-- Sport Type -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Sports</label>
                                <div class="space-y-2">
                                    @php
                                        $sports = ['Futsal', 'Badminton', 'Basket', 'Voli', 'Tenis'];
                                        $selectedSports = request('sports', []);
                                        $otherSports = array_diff((array) $selectedSports, $sports);
                                        $hasOther = count($otherSports) > 0;
                                        $otherValue = $hasOther ? reset($otherSports) : '';
                                    @endphp
                                    <div x-data="{ showOther: {{ $hasOther ? 'true' : 'false' }} }">
                                        @foreach ($sports as $sport)
                                            <label class="flex items-center gap-2 cursor-pointer group mb-2">
                                                <input type="checkbox" name="sports[]" value="{{ $sport }}"
                                                    class="w-4 h-4 rounded border-slate-300 text-accent-500 focus:ring-accent-500"
                                                    {{ in_array($sport, (array) $selectedSports) ? 'checked' : '' }}
                                                    onchange="this.form.submit()">
                                                <span class="text-sm text-slate-600 group-hover:text-slate-800">
                                                    @if($sport == 'Futsal') Futsal <i class="far fa-futbol text-blue-500 ml-1"></i> @elseif($sport == 'Badminton') Badminton <i class="fas fa-volleyball-ball text-blue-500 ml-1"></i> @elseif($sport == 'Basket') Basketball <i class="fas fa-basketball-ball text-blue-500 ml-1"></i> @elseif($sport == 'Voli') Volleyball <i class="fas fa-volleyball-ball text-blue-500 ml-1"></i> @elseif($sport == 'Tenis') Tennis <i class="fas fa-baseball-ball text-blue-500 ml-1"></i> @else {{ $sport }} @endif
                                                </span>
                                            </label>
                                        @endforeach
                                        
                                        <label class="flex items-center gap-2 cursor-pointer group mt-2">
                                            <input type="checkbox" x-model="showOther" @change="if(!showOther) { $el.form.submit(); }"
                                                class="w-4 h-4 rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                                            <span class="text-sm text-slate-600 group-hover:text-slate-800">Other (Specify)</span>
                                        </label>
                                        <div x-show="showOther" class="mt-2 pl-6">
                                            <input type="text" :name="showOther ? 'sports[]' : ''" value="{{ $otherValue }}"
                                                class="w-full text-sm py-1.5 px-3 bg-white/80 border border-slate-200 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition outline-none" 
                                                placeholder="Type sport type..." 
                                                :disabled="!showOther"
                                                onchange="this.form.submit()">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Max Price -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Max Price / hour</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 font-medium">Rp</span>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                        class="w-full pl-10 pr-4 py-2.5 bg-white/80 border border-slate-200 rounded-xl focus:ring-2 focus:ring-accent-500 focus:border-accent-500 transition shadow-inner outline-none"
                                        placeholder="Max price" min="0"
                                        onchange="this.form.submit()">
                                </div>
                                <!-- Price Range Display -->
                                <div class="mt-3 space-y-2">
                                    @php
                                        $priceRanges = [
                                            ['label' => '< Rp 50.000', 'val' => 50000],
                                            ['label' => 'Rp 50.000 - Rp 100.000', 'val' => 100000],
                                            ['label' => 'Rp 100.000 - Rp 200.000', 'val' => 200000],
                                            ['label' => '> Rp 200.000', 'val' => 999999999],
                                        ];
                                    @endphp
                                    @foreach ($priceRanges as $range)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="max_price_quick" value="{{ $range['val'] }}"
                                                class="w-4 h-4 border-slate-300 text-accent-500 focus:ring-accent-500"
                                                {{ request('max_price') == $range['val'] ? 'checked' : '' }}
                                                onclick="this.form.max_price.value = this.value; this.form.submit();">
                                            <span class="text-sm text-slate-600 group-hover:text-slate-800">{{ $range['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Main Content - Field Grid -->
                <div class="flex-1">
                    <!-- Results Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                        <p class="text-slate-500 text-sm">
                            Showing <span class="font-semibold text-slate-700">{{ $fields->count() }}</span> courts
                        </p>
                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            <label class="text-sm text-slate-500 whitespace-nowrap">Sort by:</label>
                            <select name="sort" form="filter-form" onchange="document.getElementById('filter-form').submit()"
                                class="text-sm border border-slate-200 bg-white/80 backdrop-blur-sm shadow-sm rounded-xl px-3 py-2 focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none w-full sm:w-auto">
                                <option value="lowest_price" {{ request('sort') == 'lowest_price' || request('sort') == 'Harga Terendah' ? 'selected' : '' }}>Lowest Price</option>
                                <option value="highest_price" {{ request('sort') == 'highest_price' || request('sort') == 'Harga Tertinggi' ? 'selected' : '' }}>Highest Price</option>
                                <option value="popular" {{ request('sort') == 'popular' || request('sort') == 'Terpopuler' ? 'selected' : '' }}>Most Popular</option>
                                <option value="newest" {{ request('sort') == 'newest' || request('sort') == 'Terbaru' ? 'selected' : '' }}>Newest</option>
                            </select>
                        </div>
                    </div>

                    @if ($fields->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                            @foreach ($fields as $field)
                                <div class="bg-white rounded-2xl border border-slate-200/60 overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col group h-full">
                                    <!-- Image section with badges -->
                                    <div class="relative h-52 bg-slate-100 overflow-hidden flex-shrink-0">
                                        <img src="{{ $field->image ? asset('storage/' . $field->image) : 'https://images.unsplash.com/photo-1544919982-b61976f0ba43?auto=format&fit=crop&q=80&w=800' }}"
                                            alt="{{ $field->name_fields }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                                        
                                        <!-- Gradient Overlay -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent opacity-80"></div>
                                        
                                        <!-- Sport Type Badge -->
                                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-xl shadow-md border border-white/20">
                                            <span class="text-xs font-bold text-primary-700 flex items-center gap-1.5">
                                                @if(strtolower($field->type_fields) == 'futsal')
                                                    ⚽
                                                @elseif(strtolower($field->type_fields) == 'badminton')
                                                    🏸
                                                @elseif(strtolower($field->type_fields) == 'basket' || strtolower($field->type_fields) == 'basketball')
                                                    🏀
                                                @elseif(strtolower($field->type_fields) == 'tenis' || strtolower($field->type_fields) == 'tennis')
                                                    🎾
                                                @elseif(strtolower($field->type_fields) == 'voli' || strtolower($field->type_fields) == 'volleyball')
                                                    🏐
                                                @else
                                                    🏆
                                                @endif
                                                {{ $field->type_fields }}
                                            </span>
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="absolute top-4 right-4">
                                            <span class="bg-green-500 text-white text-xs font-bold px-3 py-1.5 rounded-xl shadow-md flex items-center gap-1.5 border border-green-400">
                                                <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                                                Available
                                            </span>
                                        </div>

                                        <!-- Rating overlay on bottom-right of image -->
                                        <div class="absolute bottom-3 right-4 flex items-center gap-1 text-amber-400 bg-slate-900/60 backdrop-blur-md px-2.5 py-1 rounded-lg text-xs font-bold text-white">
                                            <i class="fas fa-star text-amber-400"></i> 4.8
                                        </div>
                                        
                                        <!-- Subcourts count on bottom-left of image -->
                                        <div class="absolute bottom-3 left-4 flex items-center gap-1 text-slate-200 bg-slate-900/60 backdrop-blur-md px-2.5 py-1 rounded-lg text-xs font-semibold">
                                            <i class="fas fa-th-large text-slate-300"></i>
                                            {{ count($field->sub_courts ?? []) }} {{ count($field->sub_courts ?? []) > 1 ? 'Courts' : 'Court' }}
                                        </div>
                                    </div>

                                    <!-- Content section -->
                                    <div class="p-4 sm:p-6 flex flex-col flex-grow">
                                        <!-- Title and Address -->
                                        <div class="mb-4">
                                            <h3 class="text-lg font-bold text-slate-800 group-hover:text-primary-600 transition-colors line-clamp-1 mb-1">{{ $field->name_fields }}</h3>
                                            <p class="text-slate-500 text-sm flex items-start gap-1.5">
                                                <i class="fas fa-map-marker-alt text-red-500 mt-1 flex-shrink-0"></i>
                                                <span class="line-clamp-2 leading-snug">{{ $field->address }}</span>
                                            </p>
                                        </div>

                                        <!-- Key specifications/Badges -->
                                        <div class="flex flex-wrap gap-2 mb-6 mt-auto">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-medium text-slate-600">
                                                <i class="fas fa-users text-slate-400"></i>
                                                Max {{ $field->capacity }} Players
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-medium text-slate-600">
                                                <i class="fas fa-tag text-slate-400"></i>
                                                Best Price
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 rounded-lg text-xs font-medium text-slate-600">
                                                <i class="fas fa-bolt text-slate-400"></i>
                                                Instant Book
                                            </span>
                                        </div>

                                        <!-- Price and Action CTA -->
                                        <div class="flex items-center justify-between pt-4 border-t border-slate-100 flex-shrink-0">
                                            <div>
                                                <span class="text-xs text-slate-400 block font-medium">Starting from</span>
                                                <div class="flex items-baseline gap-0.5">
                                                    <span class="text-xl font-extrabold text-slate-900">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }}</span>
                                                    <span class="text-xs text-slate-500">/hr</span>
                                                </div>
                                            </div>
                                            <a href="{{ Auth::check() ? route('fields.show', $field->id_fields) : route('login') }}"
                                                class="bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white px-5 py-3 rounded-xl font-bold text-sm transition shadow-md hover:shadow-accent-500/25 flex items-center gap-1.5">
                                                Book Now
                                                <i class="fas fa-arrow-right text-xs"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            {{ $fields->links() }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="bg-white/70 backdrop-blur-xl rounded-3xl border border-white/60 p-12 text-center shadow-xl">
                            <div class="text-7xl mb-6">🔍</div>
                            <h3 class="text-xl font-bold text-slate-700 mb-2">No courts found</h3>
                            <p class="text-slate-500 mb-6">Try changing your search filters or clear existing ones</p>
                            <a href="{{ route('fields.index') }}"
                                class="bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white font-bold px-8 py-3 rounded-xl transition shadow-lg inline-flex items-center gap-2">
                                <i class="fas fa-times"></i>
                                Clear All Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
