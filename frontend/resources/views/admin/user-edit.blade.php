@extends('layouts.admin')

@section('title', 'Edit User - ActiveCourt')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">Edit User</h3>
        </div>

        <form action="{{ route('admin.users.update', $user->id_users) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Role</label>
                <select name="role" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="guest" {{ $user->role == 'guest' ? 'selected' : '' }}>Guest</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white text-sm font-bold px-6 py-2.5 rounded-lg transition shadow-sm">
                    <i class="fas fa-save mr-1"></i> Save Changes
                </button>
                <a href="{{ route('admin.users') }}" class="bg-white border border-slate-200 text-slate-600 text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-slate-50 transition shadow-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
