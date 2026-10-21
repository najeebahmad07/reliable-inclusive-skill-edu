@extends('layouts.app')

@section('title', 'My Profile')
@section('breadcrumb', 'Account / Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">My Account Profile</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage your personal and center administrative information</p>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="bg-white dark:bg-[#151c2c] p-6 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Your Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Email Address</label>
                <input type="email" disabled value="{{ $user->email }}"
                       class="w-full px-3 py-2 bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-400 dark:text-slate-500 cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Contact Phone</label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Designation</label>
                <input type="text" name="designation" value="{{ old('designation', $user->designation) }}" placeholder="e.g. Center Director"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Institute Name</label>
                <input type="text" name="institute_name" value="{{ old('institute_name', $user->institute_name) }}"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Institute Address</label>
                <textarea name="institute_address" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('institute_address', $user->institute_address) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">City</label>
                <input type="text" name="city" value="{{ old('city', $user->city) }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">District</label>
                <input type="text" name="district" value="{{ old('district', $user->district) }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
        </div>

        <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs transition">
                Update Profile
            </button>
        </div>
    </form>
</div>
@endsection
