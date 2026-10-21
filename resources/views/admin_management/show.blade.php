@extends('layouts.app')

@section('title', $admin->institute_name ?? $admin->name)
@section('breadcrumb', 'Centers / ' . ($admin->institute_name ?? $admin->name))

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-xs">
        <div>
            <div class="flex items-center space-x-2.5">
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $admin->institute_name ?? $admin->name }}</h1>
                @if($admin->status === 'active')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                    Active
                </span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                    Inactive
                </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Admin: <strong class="text-slate-700 dark:text-slate-300">{{ $admin->name }}</strong> &bull; {{ $admin->email }} &bull; {{ $admin->phone_number }}</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin_management.edit', $admin->id) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-xs font-medium transition flex items-center space-x-1.5">
                <i class="fa-solid fa-pen text-xs"></i>
                <span>Edit Center</span>
            </a>
            <a href="{{ route('certificates.admin_certificate', $admin->id) }}" target="_blank" class="px-3.5 py-2 bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/40 dark:hover:bg-purple-900/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-lg text-xs font-semibold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-certificate text-xs"></i>
                <span>Authorization Certificate</span>
            </a>
        </div>
    </div>

    <!-- Wallet Adjustment & Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Balance Card -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white p-5 rounded-xl border border-slate-700/80 shadow-md flex flex-col justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">Center Wallet Balance</span>
                <div class="text-3xl font-extrabold text-white mt-2 font-mono tracking-tight">₹{{ number_format($admin->wallet_balance, 2) }}</div>
            </div>
            <div class="text-xs text-emerald-400 flex items-center space-x-1.5 mt-3 pt-3 border-t border-slate-700/60">
                <i class="fa-solid fa-circle-check text-xs"></i>
                <span>Available for student approvals</span>
            </div>
        </div>

        <!-- Adjust Wallet Form -->
        <div class="md:col-span-2 bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center space-x-2">
                <i class="fa-solid fa-money-bill-transfer text-emerald-500 text-base"></i>
                <span>Adjust Center Wallet Balance</span>
            </h2>
            <form method="POST" action="{{ route('admin_management.adjust_wallet', $admin->id) }}" class="flex flex-wrap items-end gap-3 text-sm">
                @csrf
                <div class="w-32">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Action</label>
                    <select name="type" required class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                        <option value="credit">Credit (+)</option>
                        <option value="debit">Debit (-)</option>
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Amount (₹)</label>
                    <input type="number" name="amount" required min="1" step="0.01" placeholder="1000" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Reason / Description</label>
                    <input type="text" name="description" required placeholder="Bank transfer, cash, penalty..." class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-sm shadow-xs transition">
                    Apply
                </button>
            </form>
        </div>
    </div>

    <!-- Center Details & Facilities -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 mb-4 flex items-center space-x-2">
            <i class="fa-solid fa-building text-brand-500"></i>
            <span>Center Infrastructure & Registration Details</span>
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
            <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70">
                <span class="text-slate-400 dark:text-slate-500 block text-xs font-medium">Total PCs</span>
                <span class="font-bold text-slate-800 dark:text-slate-100 text-lg mt-0.5 block font-mono">{{ $admin->total_pc ?? 0 }}</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70">
                <span class="text-slate-400 dark:text-slate-500 block text-xs font-medium">Total Staff</span>
                <span class="font-bold text-slate-800 dark:text-slate-100 text-lg mt-0.5 block font-mono">{{ $admin->total_staffs ?? 0 }}</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70">
                <span class="text-slate-400 dark:text-slate-500 block text-xs font-medium">Practical Labs</span>
                <span class="font-bold text-slate-800 dark:text-slate-100 text-lg mt-0.5 block font-mono">{{ $admin->practical_labs ?? 0 }}</span>
            </div>
            <div class="p-3 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70">
                <span class="text-slate-400 dark:text-slate-500 block text-xs font-medium">Theory Rooms</span>
                <span class="font-bold text-slate-800 dark:text-slate-100 text-lg mt-0.5 block font-mono">{{ $admin->theory_rooms ?? 0 }}</span>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-sm grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-700 dark:text-slate-300">
            <div><strong class="text-slate-900 dark:text-white">Address:</strong> {{ $admin->institute_address ?? 'N/A' }}</div>
            <div><strong class="text-slate-900 dark:text-white">Location:</strong> {{ $admin->city }}, {{ $admin->district }}, {{ $admin->institute_state }}</div>
            <div><strong class="text-slate-900 dark:text-white">Registration No:</strong> <span class="font-mono">{{ $admin->registered_no ?? 'N/A' }}</span></div>
            <div><strong class="text-slate-900 dark:text-white">Registration Type:</strong> {{ $admin->registration_type ?? 'N/A' }}</div>
        </div>
    </div>
</div>
@endsection
