@extends('layouts.app')

@section('title', 'Change Password')
@section('breadcrumb', 'Account / Change Password')

@section('content')
<div class="max-w-md mx-auto space-y-4">
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Change Password</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ensure your account uses a strong, secure password</p>
    </div>

    <form method="POST" action="{{ route('profile.change_password.update') }}" class="bg-white dark:bg-[#151c2c] p-6 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
        @csrf

        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Current Password *</label>
            <input type="password" name="current_password" required placeholder="••••••••"
                   class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">New Password (Min 6 chars) *</label>
            <input type="password" name="password" required minlength="6" placeholder="••••••••"
                   class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Confirm New Password *</label>
            <input type="password" name="password_confirmation" required minlength="6" placeholder="••••••••"
                   class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
        </div>

        <div class="flex items-center justify-end pt-2">
            <button type="submit" class="w-full py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs transition">
                Update Password
            </button>
        </div>
    </form>
</div>
@endsection
