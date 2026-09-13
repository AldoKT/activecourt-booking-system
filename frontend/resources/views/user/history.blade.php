@extends('layouts.app')

@section('title', 'Bookings History - ActiveCourt')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Bookings History</h1>
                <p class="text-slate-500 text-sm mt-1">Manage your upcoming and past court reservations.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-filter text-slate-400"></i> Filter
                </button>
                <button class="bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm transition">
                    <i class="fas fa-download text-slate-400"></i> Export
                </button>
            </div>
        </div>

        <div class="flex overflow-x-auto gap-2 mb-6 hide-scrollbar pb-2">
            <a href="{{ route('user.history', ['tab' => 'all']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ $tab === 'all' ? 'bg-primary-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">All</a>
            <a href="{{ route('user.history', ['tab' => 'waiting']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ $tab === 'waiting' ? 'bg-amber-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Waiting for Payment</a>
            <a href="{{ route('user.history', ['tab' => 'paid']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ $tab === 'paid' ? 'bg-green-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Paid</a>
            <a href="{{ route('user.history', ['tab' => 'done']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ $tab === 'done' ? 'bg-slate-700 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Done</a>
            <a href="{{ route('user.history', ['tab' => 'reschedule']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ $tab === 'reschedule' ? 'bg-blue-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Reschedule</a>
            <a href="{{ route('user.history', ['tab' => 'canceled']) }}" class="px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap {{ $tab === 'canceled' ? 'bg-red-500 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">Canceled</a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-6 text-center">BOOKING ID</th>
                            <th class="py-4 px-6 text-center">FIELD NAME</th>
                            <th class="py-4 px-6 text-center">DATE & TIME</th>
                            <th class="py-4 px-6 text-center">PRICE</th>
                            <th class="py-4 px-6 text-center">STATUS</th>
                            <th class="py-4 px-6 text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-center">
                        @forelse($bookings as $booking)
                            @php
                                $schedulesList = $booking->getSchedulesList();
                                $field = $booking->schedule?->field;

                                $createdAt = \Carbon\Carbon::parse($booking->created_at);
                                $expiresAt = $createdAt->copy()->addMinutes(30);
                                $now = \Carbon\Carbon::now();
                                $remainingSeconds = $now->lessThan($expiresAt) ? $now->diffInSeconds($expiresAt, false) : 0;

                                $playDate = \Carbon\Carbon::parse($booking->play_date);
                                $daysToPlay = $now->diffInDays($playDate, false);
                                $canCancelPaid = $booking->status_bookings === 'Paid' && $daysToPlay >= 3;
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition group"
                                x-data="timerComponent({{ $remainingSeconds }})"
                                x-init="initTimer()">
                                <td class="py-4 px-6 font-semibold text-slate-700 text-center whitespace-nowrap">{{ $booking->booking_code }}</td>
                                <td class="py-4 px-6 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                                            <i class="fas fa-volleyball-ball text-xs"></i>
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ $field->name_fields ?? 'Unknown Field' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-600 text-center whitespace-nowrap">
                                    <div class="font-medium">{{ \Carbon\Carbon::parse($booking->play_date)->format('d M Y') }}</div>
                                    @if($schedulesList->isNotEmpty())
                                        <div class="text-xs text-slate-400 mt-0.5">{{ $schedulesList->map(fn($s) => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5))->implode(', ') }} ({{ $schedulesList->count() }} hrs)</div>
                                    @else
                                        <div class="text-xs text-slate-400 mt-0.5 italic">Schedule unavailable</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800 text-center whitespace-nowrap">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if($booking->custom_status === 'paid')
                                        <span class="px-3 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-full border border-green-200">Paid</span>
                                    @elseif($booking->custom_status === 'done')
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded-full border border-slate-200">Done</span>
                                    @elseif($booking->custom_status === 'waiting')
                                        <span class="px-3 py-1 bg-amber-50 text-amber-600 text-xs font-bold rounded-full border border-amber-200">Waiting for Payment</span>
                                        @if($remainingSeconds > 0)
                                            <div class="text-[11px] text-slate-500 mt-1">
                                                ⏱ <span x-text="display"></span>
                                            </div>
                                        @endif
                                    @elseif($booking->custom_status === 'reschedule')
                                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-full border border-blue-200">Rescheduled</span>
                                    @elseif($booking->custom_status === 'canceled')
                                        <span class="px-3 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-full border border-red-200">Canceled</span>
                                    @else
                                        <span class="px-3 py-1 bg-slate-50 text-slate-600 text-xs font-bold rounded-full border border-slate-200">Unknown</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center whitespace-nowrap">
                                    @if($booking->custom_status === 'paid' || $booking->custom_status === 'done')
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('booking.invoice', $booking->id_bookings) }}" class="bg-primary-50 text-primary-600 hover:bg-primary-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-primary-100 flex items-center gap-1.5">
                                                <i class="fas fa-file-invoice text-xs"></i> Invoice
                                            </a>
                                            @if($canCancelPaid && $booking->custom_status === 'paid')
                                                <button type="button" onclick="confirmCancel('{{ route('booking.cancel', $booking->id_bookings) }}')" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-red-100">Cancel</button>
                                                <button type="button" onclick="confirmReschedule('{{ route('booking.reschedule', $booking->id_bookings) }}')" class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-blue-100">Reschedule</button>
                                            @endif
                                            <a href="{{ route('booking.show', $booking->id_bookings) }}"
                                                class="text-slate-500 hover:text-primary-600 font-semibold text-sm transition ml-2">
                                                    View Details
                                            </a>
                                        </div>
                                    @elseif($booking->custom_status === 'waiting')
                                        <div class="flex items-center justify-center gap-2">
                                            @if($remainingSeconds > 0)
                                                <button type="button" onclick="confirmCancel('{{ route('booking.cancel', $booking->id_bookings) }}')" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm font-semibold transition border border-red-100">
                                                    Cancel
                                                </button>
                                                <a href="{{ route('booking.checkout', $booking->id_bookings) }}" class="bg-accent-500 hover:bg-accent-600 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition shadow-sm hover:shadow-md flex items-center gap-1.5">
                                                    <i class="fas fa-credit-card text-xs"></i> Pay
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400 font-medium italic">Expired</span>
                                            @endif
                                        </div>
                                    @elseif($booking->custom_status === 'reschedule')
                                        <span class="text-slate-500 font-bold text-sm">Reschedule</span>
                                    @elseif($booking->custom_status === 'canceled')
                                        <span class="text-slate-500 font-bold text-sm">Canceled</span>
                                    @else
                                        <span class="text-slate-400 font-medium text-sm">Unavailable</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-8 text-slate-500">No bookings found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <span class="text-sm text-slate-500">Showing {{ $bookings->count() }} bookings</span>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('timerComponent', (remainingSeconds) => ({
            remaining: parseInt(remainingSeconds),
            timerId: null,
            get display() {
                if (this.remaining <= 0) return '00:00';
                const mins = Math.floor(this.remaining / 60);
                const secs = this.remaining % 60;
                return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
            },
            initTimer() {
                if (this.remaining <= 0) return;
                this.timerId = setInterval(() => {
                    if (this.remaining > 0) {
                        this.remaining--;
                    } else {
                        clearInterval(this.timerId);
                        location.reload();
                    }
                }, 1000);
            },
            destroy() {
                if (this.timerId) clearInterval(this.timerId);
            }
        }));
    });

    function confirmCancel(url) {
        Swal.fire({
            title: 'Cancel Booking',
            text: 'Are you sure you want to cancel this booking?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Yes, Cancel',
            cancelButtonText: 'Back'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `@csrf <input type="hidden" name="cancel_reason" value="Cancelled by user">`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmReschedule(url) {
        Swal.fire({
            title: 'Reschedule Booking',
            text: 'Are you sure you want to reschedule this booking?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Yes, Reschedule',
            cancelButtonText: 'Back'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `@csrf`;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    @if(session('success_cancel'))
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Canceled!',
            text: '{{ session("success_cancel") }}',
            icon: 'success',
            confirmButtonColor: '#3b82f6'
        });
    });
    @endif

    @if(session('success_reschedule'))
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: 'Rescheduled!',
            text: '{{ session("success_reschedule") }}',
            icon: 'success',
            confirmButtonColor: '#3b82f6'
        });
    });
    @endif
</script>
@endpush
