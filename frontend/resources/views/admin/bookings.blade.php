@extends('layouts.admin')

@section('title', 'Bookings - ActiveCourt')
@section('page-title', 'Bookings Management')

@section('content')

<div x-data="{ 
    search: '', 
    status: 'All Status', 
    date: '',
    matches(row) {
        const matchesSearch = this.search === '' || 
            row.code.toLowerCase().includes(this.search.toLowerCase()) || 
            row.user.toLowerCase().includes(this.search.toLowerCase()) || 
            row.field.toLowerCase().includes(this.search.toLowerCase());
            
        // Map status names to account for 'Waiting for Payment' vs 'Pending'
        let rowStatus = row.status.toLowerCase();
        if (rowStatus === 'waiting for payment') {
            rowStatus = 'pending';
        }
        
        let filterStatus = this.status.toLowerCase();
        if (filterStatus === 'waiting for payment') {
            filterStatus = 'pending';
        }

        const matchesStatus = this.status === 'All Status' || 
            rowStatus === filterStatus;
            
        const matchesDate = this.date === '' || 
            row.date === this.date;
            
        return matchesSearch && matchesStatus && matchesDate;
    }
}">

    <!-- Booking Filters -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px]">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" x-model="search" placeholder="Search booking..." class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
            </div>
            <select x-model="status" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                <option value="All Status">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Paid">Paid</option>
                <option value="Done">Done</option>
                <option value="Rescheduled">Rescheduled</option>
                <option value="Cancelled">Cancelled</option>
            </select>
            <input type="date" x-model="date" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
            <button @click="search = ''; status = 'All Status'; date = ''" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold px-4 py-2 rounded-lg transition border border-slate-200">
                Reset
            </button>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Booking ID</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Field</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Date & Time</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Payment</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings ?? [] as $booking)
                    <tr class="hover:bg-slate-50 transition" 
                        x-show="matches({ 
                            code: '{{ $booking->booking_code }}', 
                            user: '{{ $booking->user->name_users ?? '' }}', 
                            field: '{{ $booking->schedule->field->name_fields ?? '' }}', 
                            status: '{{ $booking->computed_status }}', 
                            date: '{{ $booking->play_date }}' 
                        })">
                        <td class="py-4 px-6">
                            <span class="font-bold text-primary-700">{{ $booking->booking_code }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($booking->user->name_users ?? 'U', 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-slate-700">{{ $booking->user->name_users ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600">{{ $booking->schedule->field->name_fields ?? '-' }}</td>
                        <td class="py-4 px-6">
                            <span class="text-sm text-slate-700">{{ $booking->play_date ? \Carbon\Carbon::parse($booking->play_date)->format('d M Y') : '-' }}</span>
                            <br>
                            <span class="text-xs text-slate-400">
                                {{ $booking->schedule ? substr($booking->schedule->start_time, 0, 5) : '-' }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="font-semibold text-slate-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($booking->payment && $booking->payment->status_payments === 'settlement')
                                <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">Settlement</span>
                            @elseif($booking->payment && $booking->payment->status_payments === 'pending')
                                <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Pending</span>
                            @elseif($booking->payment && $booking->payment->status_payments === 'failed')
                                <span class="text-xs font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full">Failed</span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($booking->computed_status === 'Paid')
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Paid</span>
                            @elseif($booking->computed_status === 'Done')
                                <span class="px-2.5 py-1 bg-slate-200 text-slate-700 text-xs font-bold rounded-full">Done</span>
                            @elseif($booking->computed_status === 'Waiting for Payment')
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">Pending</span>
                            @elseif($booking->computed_status === 'Rescheduled')
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Rescheduled</span>
                            @else
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Cancelled</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.bookings.detail', $booking->id_bookings) }}" class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition flex items-center justify-center" title="Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($booking->status_bookings === 'Paid')
                                <a href="{{ route('admin.bookings.invoice', $booking->id_bookings) }}" target="_blank" class="w-8 h-8 bg-primary-50 hover:bg-primary-100 text-primary-600 rounded-lg transition flex items-center justify-center" title="Invoice">
                                    <i class="fas fa-file-invoice text-xs"></i>
                                </a>
                                @endif
                                @if($booking->status_bookings === 'Pending')
                                <form action="{{ route('admin.bookings.forcePaid', $booking->id_bookings) }}" method="POST" class="inline" data-confirm="Force Paid booking {{ $booking->booking_code }}?">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 bg-green-50 hover:bg-green-100 text-green-600 rounded-lg transition flex items-center justify-center" title="Force Paid">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.bookings.cancel', $booking->id_bookings) }}" method="POST" class="inline" data-confirm="Cancel booking {{ $booking->booking_code }}?">
                                    @csrf
                                    <button type="submit" class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition flex items-center justify-center" title="Cancel">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="text-5xl text-blue-500 mb-3"><i class="fas fa-clipboard-list"></i></div>
                            <p class="text-slate-500 font-medium">No bookings recorded yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
