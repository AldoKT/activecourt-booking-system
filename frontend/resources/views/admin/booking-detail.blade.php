@extends('layouts.admin')

@section('title', 'Booking Detail - ActiveCourt')
@section('page-title', 'Booking Detail')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-primary-700 px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-ticket-alt"></i> Booking Details
                    </h1>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-black text-white">{{ $booking->booking_code }}</p>
                    <p class="text-primary-200 text-xs mt-1">Booking Code</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Status -->
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Booking Information</h2>
                    <p class="text-sm text-slate-500 mt-1">Complete court booking details</p>
                </div>
                @if($booking->computed_status === 'Paid')
                    <span class="px-4 py-2 bg-green-100 text-green-700 text-sm font-bold rounded-full border border-green-200 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i> PAID
                    </span>
                @elseif($booking->computed_status === 'Done')
                    <span class="px-4 py-2 bg-slate-200 text-slate-700 text-sm font-bold rounded-full border border-slate-300 flex items-center gap-2">
                        <i class="fas fa-check-double"></i> DONE
                    </span>
                @elseif($booking->computed_status === 'Waiting for Payment')
                    <span class="px-4 py-2 bg-amber-100 text-amber-700 text-sm font-bold rounded-full border border-amber-200 flex items-center gap-2">
                        <i class="fas fa-clock"></i> PENDING
                    </span>
                @elseif($booking->computed_status === 'Rescheduled')
                    <span class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-bold rounded-full border border-blue-200 flex items-center gap-2">
                        <i class="fas fa-calendar-alt"></i> RESCHEDULED
                    </span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-700 text-sm font-bold rounded-full border border-red-200 flex items-center gap-2">
                        <i class="fas fa-times-circle"></i> CANCELLED
                    </span>
                @endif
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-slate-50 rounded-lg p-5">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Customer Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Name</span>
                            <span class="font-semibold text-slate-800">{{ $booking->user->name_users ?? $booking->user->username }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Email</span>
                            <span class="font-semibold text-slate-800">{{ $booking->user->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Role</span>
                            <span class="font-semibold text-slate-800 capitalize">{{ $booking->user->role }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 rounded-lg p-5">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Booking Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Booking Code</span>
                            <span class="font-semibold text-primary-700">{{ $booking->booking_code }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Booking Date</span>
                            <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sub Court</span>
                            <span class="font-semibold text-slate-800">{{ $booking->subcourt_name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Field Detail -->
            <div class="mb-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Field Details</h3>
                <div class="bg-slate-50 rounded-lg p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-volleyball-ball text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-slate-800 text-lg">{{ $booking->schedule->field->name_fields ?? '-' }}</h4>
                            <p class="text-sm text-slate-500 mt-0.5">{{ $booking->schedule->field->type_fields ?? '-' }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $booking->schedule->field->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jadwal -->
            @php
                $schedulesList = $booking->getSchedulesList();
                $field = $booking->schedule ? $booking->schedule->field : null;
            @endphp
            <div class="mb-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Rental Schedule</h3>
                <div class="bg-slate-50 rounded-lg p-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-slate-500 block">Date</span>
                            <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($booking->play_date)->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Time</span>
                            <span class="font-bold text-slate-800">
                                {{ $schedulesList->map(fn($s) => substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5))->implode(', ') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Duration</span>
                            <span class="font-bold text-slate-800">{{ $schedulesList->count() }} hrs</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Detail -->
            <div class="mb-8">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Payment Details</h3>
                <div class="bg-slate-50 rounded-lg p-5">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Price per Hour</span>
                            <span class="font-semibold text-slate-800">Rp {{ number_format($field->price_per_hour ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Hours</span>
                            <span class="font-semibold text-slate-800">{{ $schedulesList->count() }} hrs</span>
                        </div>
                        @if($booking->payment && $booking->payment->payment_method)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Payment Method</span>
                            <span class="font-semibold text-slate-800 capitalize">{{ str_replace('_', ' ', $booking->payment->payment_method) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-slate-500">Payment Status</span>
                            @if($booking->payment)
                                <span class="font-semibold capitalize {{ $booking->payment->status_payments === 'settlement' ? 'text-green-600' : ($booking->payment->status_payments === 'pending' ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $booking->payment->status_payments }}
                                </span>
                            @else
                                <span class="font-semibold text-slate-400">-</span>
                            @endif
                        </div>
                        @if($booking->payment && $booking->payment->paid_at)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Paid At</span>
                            <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($booking->payment->paid_at)->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                        <div class="border-t border-slate-200 pt-3 flex justify-between">
                            <span class="font-bold text-slate-800 text-base">Total Payment</span>
                            <span class="font-extrabold text-accent-600 text-xl">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancel info -->
            @if($booking->status_bookings === 'Cancelled' && $booking->cancel_reason)
            <div class="mb-8 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-red-500 mt-0.5"></i>
                    <div>
                        <p class="font-bold text-red-700 text-sm">Cancellation Reason</p>
                        <p class="text-red-600 text-sm mt-1">{{ $booking->cancel_reason }}</p>
                        @if($booking->cancelled_at)
                            <p class="text-red-500 text-xs mt-1">Cancelled at: {{ \Carbon\Carbon::parse($booking->cancelled_at)->format('d M Y, H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="border-t border-slate-200 pt-6 flex flex-wrap gap-3">
                <a href="{{ route('admin.bookings') }}" class="bg-white border border-slate-200 text-slate-600 font-bold px-5 py-2.5 rounded-lg hover:bg-slate-50 transition shadow-sm flex items-center gap-2 text-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                @if($booking->computed_status === 'Paid' || $booking->computed_status === 'Done')
                    <a href="{{ route('admin.bookings.invoice', $booking->id_bookings) }}" target="_blank" class="bg-primary-600 hover:bg-primary-700 text-white font-bold px-5 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                        <i class="fas fa-file-invoice"></i> View Invoice
                    </a>
                @endif
                @if($booking->computed_status === 'Waiting for Payment')
                    <form action="{{ route('admin.bookings.forcePaid', $booking->id_bookings) }}" method="POST" class="inline" data-confirm="Are you sure you want to force this booking to Paid?">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold px-5 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                            <i class="fas fa-check"></i> Force Paid
                        </button>
                    </form>
                    <form action="{{ route('admin.bookings.cancel', $booking->id_bookings) }}" method="POST" class="inline" data-confirm="Are you sure you want to cancel this booking?">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold px-5 py-2.5 rounded-lg transition shadow-sm flex items-center gap-2 text-sm">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
