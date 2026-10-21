@extends('layouts.app')

@section('title', 'Edit Center - ' . $admin->name)
@section('breadcrumb', 'Centers / Edit / ' . $admin->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Edit Center Details</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Update credentials, facility information, and assigned programs</p>
        </div>
        <a href="{{ route('admin_management.index') }}" class="px-3.5 py-2 text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back to Centers</span>
        </a>
    </div>

    <form method="POST" action="{{ route('admin_management.update', $admin->id) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center space-x-2">
                <i class="fa-solid fa-key text-brand-500 text-base"></i>
                <span>Login Credentials & Status</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">In-Charge Name *</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="active" {{ old('status', $admin->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $admin->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Change Password (Leave blank to keep current)</label>
                    <input type="password" name="password" minlength="6" placeholder="New password (optional)"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Contact Phone</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $admin->phone_number) }}"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center space-x-2">
                <i class="fa-solid fa-building-columns text-indigo-500 text-base"></i>
                <span>Institute Information</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Institute Name *</label>
                    <input type="text" name="institute_name" value="{{ old('institute_name', $admin->institute_name) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div class="sm:col-span-3">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Address</label>
                    <textarea name="institute_address" rows="2" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">{{ old('institute_address', $admin->institute_address) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">City</label>
                    <input type="text" name="city" value="{{ old('city', $admin->city) }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">District</label>
                    <input type="text" name="district" value="{{ old('district', $admin->district) }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">State</label>
                    <input type="text" name="institute_state" value="{{ old('institute_state', $admin->institute_state) }}" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <!-- Assigned Programs -->
        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-3 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center space-x-2">
                <i class="fa-solid fa-graduation-cap text-purple-500 text-base"></i>
                <span>Assigned Programs</span>
            </h2>

            @php
                $assignedIds = $admin->programs->pluck('id')->toArray();
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                @foreach($programs as $p)
                <label class="flex items-center space-x-2.5 p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/60 text-sm transition">
                    <input type="checkbox" name="assigned_programs[]" value="{{ $p->id }}" {{ in_array($p->id, $assignedIds) ? 'checked' : '' }} class="rounded text-brand-500 focus:ring-brand-500">
                    <span class="text-slate-800 dark:text-slate-200 font-medium truncate">{{ $p->program_name }}</span>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('admin_management.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-bold shadow-sm transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
