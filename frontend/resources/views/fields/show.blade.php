@extends('layouts.app')

@section('title', $field->name_fields . ' - ActiveCourt')

@section('content')

<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-sm text-slate-500 mb-6 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-primary-600 font-medium transition">Home</a> 
            <span>&rsaquo;</span> 
            <a href="{{ route('fields.index') }}" class="hover:text-primary-600 font-medium transition">Fields</a> 
            <span>&rsaquo;</span> 
            <span class="text-primary-700 font-bold">{{ $field->name_fields }}</span>
        </div>

        <div class="relative h-[350px] rounded-2xl overflow-hidden mb-8 shadow-sm border border-slate-200">
            <img src="{{ $field->image ? asset('storage/' . $field->image) : 'https://images.unsplash.com/photo-1544919982-b61976f0ba43?auto=format&fit=crop&q=80&w=2000' }}" 
                 alt="{{ $field->name_fields }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>
            
            <div class="absolute bottom-6 left-6 right-6 flex justify-between items-end">
                <div>
                    <span class="bg-primary-500 text-white text-[10px] font-bold px-2.5 py-1 rounded uppercase tracking-wider mb-2 inline-block">
                        {{ $field->type_fields }}
                    </span>
                    <h1 class="text-3xl font-bold text-white mb-2">{{ $field->name_fields }}</h1>
                    <p class="text-white/80 text-sm flex items-center gap-1.5 font-medium">
                        <i class="fas fa-map-marker-alt text-primary-400"></i> {{ $field->address }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <button class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white hover:bg-white/40 border border-white/30 transition flex items-center justify-center"><i class="fas fa-share-alt"></i></button>
                    <button class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md text-white hover:bg-white/40 border border-white/30 transition flex items-center justify-center"><i class="far fa-heart"></i></button>
                </div>
            </div>
        </div>

        <div id="booking-container"
             data-schedules="{{ $schedulesJson }}"
             data-price="{{ $field->price_per_hour }}"
             data-subcourts="{{ $subcourtsJson }}"
             data-today="{{ \Carbon\Carbon::today()->toDateString() }}"
             class="flex flex-col lg:flex-row gap-8 lg:items-start" x-data="bookingForm()">
            
            <div class="flex-1 space-y-6">
                
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-slate-800">Field Information</h2>
                        <span class="bg-primary-50 text-primary-700 text-xs font-bold px-3 py-1.5 rounded-md border border-primary-100 uppercase tracking-wide">Premium Facility</span>
                    </div>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">{{ $field->description }}</p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @forelse($field->facilities as $facility)
                            <div class="flex items-center gap-2.5 text-slate-700 text-sm font-medium">
                                <i class="fas fa-{{ $facility->icon ?? 'check-circle' }} text-blue-500 text-base w-5 text-center"></i> 
                                {{ $facility->name_facilities }}
                            </div>
                        @empty
                            <p class="text-slate-400 text-sm col-span-4">No facilities added yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
                    <h2 class="text-xl font-bold text-slate-800 mb-5">Select Court Division</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($field->sub_courts as $sub)
                            @php 
                                $subName = is_array($sub) ? ($sub['name'] ?? 'Sub-Court') : $sub;
                                $subType = is_array($sub) ? ($sub['type'] ?? 'Standard Court') : 'Standard Court';
                            @endphp
                            <label class="bg-white border-2 rounded-xl p-4 cursor-pointer relative transition-all block"
                                   :class="selectedSubcourt === '{{ $subName }}' ? 'border-primary-500 shadow-[0_0_0_4px_rgba(59,130,246,0.1)]' : 'border-slate-200 hover:border-slate-300'">
                                <input type="radio" name="subcourt" value="{{ $subName }}" x-model="selectedSubcourt" class="hidden">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Available</span>
                                    <i class="fas fa-running transition-colors" :class="selectedSubcourt === '{{ $subName }}' ? 'text-primary-500' : 'text-slate-400'"></i>
                                </div>
                                <div class="font-bold text-slate-800 text-lg">{{ $subName }}</div>
                                <div class="text-xs text-slate-500 mt-1 font-medium">{{ $subType }}</div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                        <h2 class="text-xl font-bold text-slate-800">Available Schedule</h2>
                        
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-slate-600">Choose Date:</label>
                            <input type="date" x-model="playDate" @change="handleDateChange($event.target.value)"
                                   min="{{ \Carbon\Carbon::today()->toDateString() }}"
                                   class="border border-slate-300 text-slate-700 text-sm rounded-lg px-3 py-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all cursor-pointer bg-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        <template x-for="schedule in filteredSchedules" :key="schedule.id_schedules">
                            <button type="button"
                                x-on:click="handleTimeClick(schedule)"
                                :disabled="checkDisabled(schedule)"
                                :class="getButtonClass(schedule)"
                                class="border py-2.5 rounded-lg text-sm font-bold transition-all"
                                x-text="formatTime(schedule.start_time)">
                            </button>
                        </template>
                        <div x-show="filteredSchedules.length === 0" class="col-span-full text-center py-6 text-sm text-slate-500 font-medium" style="display: none;">
                            No schedules available for this date.
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-96 sticky top-24">
                <form method="POST" action="{{ route('booking.store') }}">
                    @csrf
                    <template x-for="id in selectedScheduleIds" :key="id">
                        <input type="hidden" name="schedule_ids[]" :value="id">
                    </template>
                    <input type="hidden" name="play_date" :value="playDate">
                    <input type="hidden" name="subcourt_name" :value="selectedSubcourt">

                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 md:p-8">
                        <h3 class="text-xl font-bold text-slate-800 mb-6 border-b border-slate-100 pb-4">Order Summary</h3>
                        
                        <div class="space-y-4 text-sm mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Court</span> 
                                <span class="font-bold text-primary-700" x-text="selectedSubcourt"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Time</span> 
                                <span x-text="selectedTimeRange" class="font-bold text-slate-800 text-right">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Base Price</span> 
                                <span class="font-bold text-slate-800">Rp {{ number_format($field->price_per_hour, 0, ',', '.') }} <span class="text-slate-400 font-normal">/ hr</span></span>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-200 flex justify-between items-center mb-8">
                            <span class="font-bold text-slate-800 text-lg">Total</span>
                            <span x-text="formatCurrency(totalPrice)" class="font-extrabold text-2xl text-accent-600">Rp 0</span>
                        </div>

                        @auth
                            <button type="submit" 
                                    :disabled="selectedScheduleIds.length === 0"
                                    class="w-full font-bold py-3.5 rounded-lg mb-3 flex justify-center items-center gap-2 transition-all"
                                    :class="selectedScheduleIds.length > 0 ? 'bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white shadow-md' : 'bg-slate-200 text-slate-400 cursor-not-allowed'">
                                <i class="fas fa-check-circle"></i> Book Now
                            </button>
                        @else
                            <a href="{{ route('login') }}" 
                               class="w-full font-bold py-3.5 rounded-lg mb-3 flex justify-center items-center gap-2 transition-all bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white shadow-md cursor-pointer block text-center">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                        @endauth
                        
                        <a href="{{ route('contact') }}" class="w-full bg-white border border-slate-200 text-slate-600 font-bold py-3 rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-all text-sm flex justify-center items-center gap-2 shadow-sm">
                            <div class="w-6 h-6 rounded-full bg-accent-100 text-accent-600 flex items-center justify-center text-[10px]">PE</div>
                            Ask Owner
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function bookingForm() {
        const container = document.getElementById('booking-container');
        
        const parsedSubcourts = JSON.parse(container.dataset.subcourts);
        const initialSubcourt = (parsedSubcourts && parsedSubcourts.length > 0) ? (parsedSubcourts[0].name || parsedSubcourts[0]) : '';

        return {
            schedules: JSON.parse(container.dataset.schedules),
            pricePerHour: parseInt(container.dataset.price),
            selectedSubcourt: initialSubcourt,
            playDate: container.dataset.today,
            
            selectionStart: null,
            selectionEnd: null,
            selectedScheduleIds: [],

            init() {
                // Console.log dinonaktifkan agar bersih di production, aktifkan jika butuh debug
                // console.log("Total Schedules Loaded:", this.schedules.length);
            },

            checkDisabled(schedule) {
                if (!schedule) return true;
                
                const now = new Date();
                const todayStr = now.toISOString().split('T')[0];
                const scheduleDate = schedule.date ? schedule.date.split('T')[0] : '';

                // Check if schedule date has passed (earlier than today)
                if (scheduleDate < todayStr) {
                    return true;
                }

                // Check if schedule time has passed for today
                if (scheduleDate === todayStr) {
                    const [hours, minutes] = schedule.start_time.split(':').map(Number);
                    const scheduleTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hours, minutes);
                    if (scheduleTime <= now) {
                        return true;
                    }
                }

                const statusRaw = schedule.status_schedules;
                if (statusRaw && String(statusRaw).toLowerCase().trim() === 'locked') return true;

                if (schedule.bookings && Array.isArray(schedule.bookings)) {
                    const isBooked = schedule.bookings.some(b => 
                        b.subcourt_name === this.selectedSubcourt &&
                        (b.status_bookings === 'Pending' || b.status_bookings === 'Paid' || b.status_bookings === 'Done')
                    );
                    if (isBooked) {
                        return true;
                    }
                }
                
                return false; 
            },

            getButtonClass(schedule) {
                const isDisabled = this.checkDisabled(schedule);
                const isSelected = this.selectedScheduleIds.map(String).includes(String(schedule.id_schedules));

                if (isDisabled) {
                    return 'bg-slate-100 border-slate-200 text-slate-400 cursor-not-allowed';
                }
                if (isSelected) {
                    return 'bg-primary-600 text-white border-primary-600 shadow-md';
                }
                return 'bg-white border-slate-300 text-slate-700 hover:border-primary-500 hover:text-primary-600';
            },

            formatTime(timeString) {
                return timeString ? timeString.substring(0, 5) : '';
            },

            get filteredSchedules() {
                return this.schedules.filter(s => {
                    if (!s.date) return false;
                    const sDate = s.date.split('T')[0];
                    const pDate = this.playDate.split('T')[0];
                    return sDate === pDate;
                });
            },

            handleDateChange(date) {
                this.playDate = date;
                this.resetSelection();
            },

            handleTimeClick(clickedSchedule) {
                if (this.checkDisabled(clickedSchedule)) return;

                const clickedId = String(clickedSchedule.id_schedules);

                const index = this.selectedScheduleIds.indexOf(clickedId);

                if (index > -1) {
                    // unselect
                    this.selectedScheduleIds.splice(index, 1);
                } else {
                    // select
                    this.selectedScheduleIds.push(clickedId);
                }
            },

            resetSelection() {
                this.selectedScheduleIds = [];
            },

            get totalPrice() {
                return this.selectedScheduleIds.length * this.pricePerHour;
            },

            get selectedTimeRange() {
                if (this.selectedScheduleIds.length === 0) return '-';

                const selected = this.filteredSchedules.filter(
                    s => this.selectedScheduleIds.includes(String(s.id_schedules))
                );

                return selected
                    .map(s => this.formatTime(s.start_time))
                    .join(', ');
            },

            formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
            }
        }
    }
</script>
@endpush
@endsection
