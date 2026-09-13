@extends('layouts.app')

@section('title', 'Booking Details - ActiveCourt')

@section('content')
<div class="bg-slate-50 min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <div class="text-sm text-slate-500 mb-6 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-primary-600">Home</a>
            <span>&rsaquo;</span>
            <a href="{{ route('user.history') }}" class="hover:text-primary-600">History</a>
            <span>&rsaquo;</span>
            <span class="text-primary-700 font-bold">Booking Details</span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <!-- Header -->
            <div class="p-8 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-primary-50 flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-primary-600 text-xl"></i>
                    </div>

                    <div>
                        <h1 class="text-3xl font-bold text-slate-800">
                            Booking Details
                        </h1>
                        <p class="text-slate-500 mt-1">
                            Complete information of your court reservation
                        </p>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">

                    <!-- Kode Booking -->
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Booking Code</p>
                        <p class="font-bold text-xl text-slate-800">
                            {{ $booking->booking_code }}
                        </p>
                    </div>

                    <!-- Lapangan -->
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Field</p>
                        <p class="font-bold text-lg text-slate-800">
                            {{ $booking->schedule->field->name_fields ?? '-' }}
                        </p>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Play Date</p>
                        <p class="font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($booking->play_date)->format('d F Y') }}
                        </p>
                    </div>

                    <!-- Sub Court -->
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Sub-Court</p>
                        <p class="font-semibold text-slate-800">
                            {{ $booking->subcourt_name }}
                        </p>
                    </div>

                    <!-- Jam Bermain -->
                    <div class="md:col-span-2">
                        <p class="text-sm text-slate-500 mb-3">Play Time</p>

                        <div class="flex flex-wrap gap-2">
                            @if($booking->getSchedulesList()->isNotEmpty())
                                @foreach($booking->getSchedulesList() as $schedule)
                                    <span class="px-4 py-2 bg-primary-50 text-primary-600 rounded-full text-sm font-semibold border border-primary-100">
                                        {{ substr($schedule->start_time, 0, 5) }}
                                        -
                                        {{ substr($schedule->end_time, 0, 5) }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-slate-400">
                                    Schedule unavailable
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Booking Status</p>
                        @if($booking->computed_status === 'Paid')
                            <span class="inline-flex px-3 py-1 bg-green-50 text-green-600 rounded-full text-sm font-bold border border-green-200">
                                Paid
                            </span>
                        @elseif($booking->computed_status === 'Done')
                            <span class="inline-flex px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm font-bold border border-slate-200">
                                Done
                            </span>
                        @elseif($booking->computed_status === 'Waiting for Payment')
                            <span class="inline-flex px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-sm font-bold border border-amber-200">
                                Pending
                            </span>
                        @elseif($booking->computed_status === 'Rescheduled')
                            <span class="inline-flex px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-sm font-bold border border-blue-200">
                                Rescheduled
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 bg-red-50 text-red-600 rounded-full text-sm font-bold border border-red-200">
                                Cancelled
                            </span>
                        @endif
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Payment Method</p>
                        <p class="font-semibold text-slate-800">
                            @if($booking->computed_status === 'Paid' || $booking->computed_status === 'Done')
                                {{ $booking->payment->payment_method ?? 'Automatic' }}
                            @else
                                {{ $booking->payment->payment_method ?? 'Unpaid' }}
                            @endif
                        </p>
                    </div>

                </div>

                <!-- Total -->
                <div class="mt-10 border-t border-slate-200 pt-6">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-slate-600">
                            Total Payment
                        </span>

                        <span class="text-3xl font-extrabold text-accent-600">
                            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-10 flex flex-wrap gap-3">

                    <a href="{{ route('user.history') }}"
                       class="px-5 py-3 border border-slate-300 rounded-xl font-semibold text-slate-700 hover:bg-slate-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>

                    @if($booking->computed_status === 'Paid' || $booking->computed_status === 'Done')
                        <a href="{{ route('booking.invoice', $booking->id_bookings) }}"
                           class="px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold transition">
                            <i class="fas fa-file-invoice mr-2"></i>
                            Download Invoice
                        </a>
                    @endif

                </div>

            </div>
        </div>
    </div>
</div>
@endsection