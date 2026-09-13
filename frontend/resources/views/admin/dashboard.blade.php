@extends('layouts.admin')

@section('title', 'Dashboard Admin - ActiveCourt')
@section('page-title', 'Dashboard Overview')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-500">Total Revenue</span>
            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">Rp {{ number_format($totalRevenue ?? 42500000, 0, ',', '.') }}</p>
        <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> +12.5% from last month
        </p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-500">Active Bookings</span>
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">{{ $activeBookings ?? 24 }}</p>
        <p class="text-xs text-blue-600 mt-1 flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> +3 from last week
        </p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-500">New Users (Monthly)</span>
            <div class="w-10 h-10 bg-accent-100 text-accent-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">{{ $newUsers ?? 128 }}</p>
        <p class="text-xs text-accent-600 mt-1 flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> +18% from last month
        </p>
    </div>
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between mb-3">
            <span class="text-sm font-medium text-slate-500">Conversion Rate</span>
            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-800">{{ $conversionRate ?? 68.5 }}%</p>
        <p class="text-xs text-purple-600 mt-1 flex items-center gap-1">
            <i class="fas fa-arrow-up"></i> +2.1% from last month
        </p>
    </div>
</div>

<!-- Financial Summary -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-bold text-slate-800">Financial Summary</h3>
            <p class="text-xs text-slate-500 font-medium">Recent transaction and revenue data</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
            <span class="text-xs font-bold text-green-600 uppercase tracking-wider">Total Revenue</span>
            <p class="text-2xl font-extrabold text-green-700 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-green-500 mt-1">All-time</p>
        </div>
        <div class="bg-accent-50 rounded-lg p-4 border border-accent-100">
            <span class="text-xs font-bold text-accent-600 uppercase tracking-wider">Today's Revenue</span>
            <p class="text-2xl font-extrabold text-accent-700 mt-1">Rp {{ number_format($todayRevenue ?? 0, 0, ',', '.') }}</p>
            <p class="text-xs text-accent-500 mt-1">{{ $todayBookings ?? 0 }} bookings today</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Active Bookings</span>
            <p class="text-2xl font-extrabold text-blue-700 mt-1">{{ $activeBookings ?? 0 }}</p>
            <p class="text-xs text-blue-500 mt-1">{{ $pendingBookings ?? 0 }} pending</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Bookings -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Recent Bookings</h3>
            <a href="{{ route('admin.bookings') }}" class="text-sm text-accent-600 hover:text-accent-700 font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">Booking ID</th>
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">Field</th>
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">User</th>
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="text-left py-3 px-6 text-xs font-bold text-slate-500 uppercase">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentBookings ?? [] as $booking)
                    <tr class="hover:bg-slate-50">
                        <td class="py-3 px-6 text-sm font-semibold text-primary-700">{{ $booking->booking_code }}</td>
                        <td class="py-3 px-6 text-sm text-slate-600">{{ $booking->schedule->field->name_fields ?? '-' }}</td>
                        <td class="py-3 px-6 text-sm text-slate-600">{{ $booking->user->name_users ?? '-' }}</td>
                        <td class="py-3 px-6">
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
                        <td class="py-3 px-6 text-sm font-semibold text-slate-700">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-slate-400 text-sm">No bookings recorded yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <h3 class="font-bold text-slate-800 mb-4">Quick Stats</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-600">Total Fields</span>
                <span class="font-bold text-slate-800">{{ $totalFields ?? 12 }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-600">Total Users</span>
                <span class="font-bold text-slate-800">{{ $totalUsers ?? 256 }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-600">Today's Bookings</span>
                <span class="font-bold text-slate-800">{{ $todayBookings ?? 5 }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-slate-100">
                <span class="text-sm text-slate-600">Today's Revenue</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($todayRevenue ?? 750000, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
                <span class="text-sm text-slate-600">Pending Bookings</span>
                <span class="font-bold text-amber-600">{{ $pendingBookings ?? 3 }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
