@extends('layouts.admin')

@section('title', 'User Management - ActiveCourt')
@section('page-title', 'User Management')

@section('content')

<div x-data="{ search: '' }">

    <!-- User Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Users</p>
                    <p class="text-2xl font-extrabold text-slate-800 mt-1">{{ $totalUsers ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Active (7 days)</p>
                    <p class="text-2xl font-extrabold text-green-600 mt-1">{{ $activeNow ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-circle"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Banned</p>
                    <p class="text-2xl font-extrabold text-red-600 mt-1">{{ $banned ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">User List</h3>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" x-model="search" placeholder="Search user..." class="pl-9 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Join Date</th>
                        <th class="text-left py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="text-center py-4 px-6 text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users ?? [] as $user)
                    <tr class="hover:bg-slate-50 transition" 
                        x-show="search === '' || '{{ strtolower($user->name_users) }}'.includes(search.toLowerCase()) || '{{ strtolower($user->email) }}'.includes(search.toLowerCase())">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-accent-100 text-accent-600 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(substr($user->name_users ?? 'U', 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800">{{ $user->name_users }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600">{{ $user->email }}</td>
                        <td class="py-4 px-6">
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-primary-100 text-primary-700' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-sm text-slate-600">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d M Y') : '-' }}</td>
                        <td class="py-4 px-6">
                            @if($user->banned_at)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-red-700 bg-red-100 px-3 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                    Banned
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-green-700 bg-green-100 px-3 py-1 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Active
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.users.edit', $user->id_users) }}" class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition flex items-center justify-center" title="Edit">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                @if(auth()->id() !== $user->id_users)
                                    <form action="{{ route('admin.users.ban', $user->id_users) }}" method="POST" class="inline" 
                                          data-confirm="Are you sure you want to {{ $user->banned_at ? 'unban' : 'ban' }} user {{ $user->name_users }}?">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 {{ $user->banned_at ? 'bg-green-50 hover:bg-green-100 text-green-600' : 'bg-red-50 hover:bg-red-100 text-red-600' }} rounded-lg transition flex items-center justify-center" 
                                                title="{{ $user->banned_at ? 'Unban' : 'Ban' }}">
                                            <i class="fas fa-{{ $user->banned_at ? 'check-circle' : 'ban' }} text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <div class="text-5xl text-blue-500 mb-3"><i class="fas fa-users"></i></div>
                            <p class="text-slate-500 font-medium">No users found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
