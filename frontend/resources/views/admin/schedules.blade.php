@extends('layouts.admin')

@section('title', 'Schedule Management - ActiveCourt')
@section('page-title', 'Schedule Management')

@section('content')
<div x-data="{ addModalOpen: false }">

<!-- Add Schedule Modal -->
<div x-show="addModalOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
     x-cloak>
    
    <!-- Modal container -->
    <div @click.away="addModalOpen = false" 
         x-show="addModalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col">
         
         <!-- Modal Header -->
         <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
             <div class="flex items-center gap-3">
                 <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center">
                     <i class="fas fa-clock"></i>
                 </div>
                 <div>
                     <h3 class="font-bold text-slate-800">Generate Schedules</h3>
                     <p class="text-xs text-slate-500 font-medium">Create batch schedule slots for fields</p>
                 </div>
             </div>
             <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                 <i class="fas fa-times text-lg"></i>
             </button>
         </div>
         
         <!-- Modal Form -->
         <form action="{{ route('admin.schedules.store') }}" method="POST" class="p-6 space-y-4">
             @csrf
             <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                 <div class="sm:col-span-2">
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Field</label>
                     <select name="field_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                         <option value="">Select Field</option>
                         @foreach($fields as $field)
                             <option value="{{ $field->id_fields }}">{{ $field->name_fields }} ({{ $field->type_fields }})</option>
                         @endforeach
                     </select>
                 </div>
                 <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Start Date</label>
                     <input type="date" name="start_date" required min="{{ \Carbon\Carbon::today()->toDateString() }}" 
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                 </div>
                 <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">End Date</label>
                     <input type="date" name="end_date" required min="{{ \Carbon\Carbon::today()->toDateString() }}" 
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                 </div>
                 <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Start Time</label>
                     <input type="time" name="start_time" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                 </div>
                 <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">End Time</label>
                     <input type="time" name="end_time" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                 </div>
                 <div class="sm:col-span-2">
                     <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Interval</label>
                     <select name="interval" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                         <option value="60">60 Minutes</option>
                         <option value="90">90 Minutes</option>
                         <option value="120">120 Minutes</option>
                     </select>
                 </div>
             </div>
             
             <!-- Modal Footer Actions -->
             <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                 <button type="button" @click="addModalOpen = false" class="bg-white border border-slate-200 text-slate-700 text-sm font-bold px-5 py-2.5 rounded-lg hover:bg-slate-50 transition">
                     Cancel
                 </button>
                 <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2">
                     <i class="fas fa-plus"></i> Generate Schedule
                 </button>
             </div>
         </form>
    </div>
</div>

<!-- Schedules Table -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between px-6 py-4 border-b border-slate-100 gap-4">
        <h3 class="font-bold text-slate-800">Schedule List</h3>
        <div class="flex flex-col lg:flex-row items-center gap-4 w-full md:w-auto">
            <form action="{{ route('admin.schedules') }}" method="GET" class="flex flex-col lg:flex-row items-center gap-2 w-full md:w-auto">
                <select name="filter_field_id" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-accent-500 outline-none w-full sm:w-auto">
                    <option value="">All Fields</option>
                    @foreach($fields as $field)
                        <option value="{{ $field->id_fields }}" {{ request('filter_field_id') == $field->id_fields ? 'selected' : '' }}>
                            {{ $field->name_fields }}
                        </option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="date" name="filter_start_date" onchange="this.form.submit()"
                           class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-accent-500 outline-none w-full"
                           value="{{ request('filter_start_date', \Carbon\Carbon::today()->toDateString()) }}">
                    <span class="text-slate-400 font-medium text-sm text-center">to</span>
                    <input type="date" name="filter_end_date" onchange="this.form.submit()"
                           class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-accent-500 outline-none w-full"
                           value="{{ request('filter_end_date', \Carbon\Carbon::today()->toDateString()) }}">
                </div>
            </form>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <form action="{{ route('admin.schedules.destroyAll') }}" method="POST" data-confirm="Are you sure you want to delete all empty schedules (Available/Locked) on this filter?" class="w-full sm:w-auto">
                    @csrf
                    <input type="hidden" name="filter_field_id" value="{{ request('filter_field_id') }}">
                    <input type="hidden" name="filter_start_date" value="{{ request('filter_start_date', \Carbon\Carbon::today()->toDateString()) }}">
                    <input type="hidden" name="filter_end_date" value="{{ request('filter_end_date', \Carbon\Carbon::today()->toDateString()) }}">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-bold px-4 py-1.5 rounded-lg transition w-full sm:w-auto flex items-center justify-center gap-2">
                        <i class="fas fa-trash"></i> Delete All
                    </button>
                </form>
                <button @click="addModalOpen = true" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-4 py-1.5 rounded-lg transition w-full sm:w-auto flex items-center justify-center gap-2 shadow-sm">
                    <i class="fas fa-calendar-plus"></i> Generate
                </button>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" id="schedulesTable">
            <thead>
                <tr class="bg-slate-50">
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Field</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Start</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">End</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Booking</th>
                    <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($schedules as $schedule)
                <tr class="hover:bg-slate-50 transition" data-date="{{ $schedule->date }}">
                    <td class="py-4 px-6">
                        <span class="font-semibold text-slate-800">{{ $schedule->field->name_fields ?? '-' }}</span>
                        <br><span class="text-xs text-slate-400">{{ $schedule->field->type_fields ?? '' }}</span>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600">{{ \Carbon\Carbon::parse($schedule->date)->format('d M Y') }}</td>
                    <td class="py-4 px-6 font-semibold text-slate-700">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                    <td class="py-4 px-6 font-semibold text-slate-700">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                    <td class="py-4 px-6">
                        @if($schedule->status_schedules === 'Available')
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Available</span>
                        @elseif($schedule->status_schedules === 'Booked')
                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Booked</span>
                        @else
                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Locked</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-sm">
                        @if($schedule->status_schedules === 'Booked')
                            @php
                                $booking = \App\Models\Booking::where('schedule_id', $schedule->id_schedules)
                                    ->orWhereJsonContains('schedule_ids', $schedule->id_schedules)
                                    ->latest()->first();
                            @endphp
                            @if($booking)
                                <span class="font-medium text-slate-700">{{ $booking->user->name_users ?? 'Unknown' }}</span>
                                <br><span class="text-xs text-slate-400">{{ $booking->booking_code }}</span>
                            @else
                                <span class="text-slate-400 italic">Booked</span>
                            @endif
                        @else
                            <span class="text-slate-400 italic">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center gap-2">
                            <form action="{{ route('admin.schedules.toggle', $schedule->id_schedules) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="w-8 h-8 {{ $schedule->status_schedules === 'Available' ? 'bg-amber-50 hover:bg-amber-100 text-amber-600' : 'bg-green-50 hover:bg-green-100 text-green-600' }} rounded-lg transition flex items-center justify-center"
                                        title="{{ $schedule->status_schedules === 'Available' ? 'Lock' : 'Unlock' }}">
                                    <i class="fas fa-{{ $schedule->status_schedules === 'Available' ? 'lock' : 'lock-open' }} text-xs"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.schedules.destroy', $schedule->id_schedules) }}" method="POST" class="inline" data-confirm="Are you sure you want to delete this schedule?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition flex items-center justify-center" title="Delete">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center">
                        <div class="text-5xl text-blue-500 mb-3"><i class="fas fa-calendar-alt"></i></div>
                        <p class="text-slate-500 font-medium">No schedules found</p>
                        <p class="text-slate-400 text-sm mt-1">Create a new schedule using the form above.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection


