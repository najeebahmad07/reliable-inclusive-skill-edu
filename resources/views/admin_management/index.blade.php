@extends('layouts.app')

@section('title', 'Center Admins Management')
@section('breadcrumb', 'Administration / Centers')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Authorized Centers & Admins</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage all training centers, wallet allocations, and access permissions</p>
        </div>
        <div>
            <a href="{{ route('admin_management.create') }}" class="px-3.5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Center Admin</span>
            </a>
        </div>
    </div>

    <!-- Search / Filter -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('admin_management.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search institute name, person, email, city..."
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <select name="status" class="px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-brand-600 dark:hover:bg-brand-700 text-white rounded-lg text-sm font-medium transition">Filter</button>
            @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin_management.index') }}" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Centers Table -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th>Center / Institute</th>
                        <th>Admin / Contact</th>
                        <th>Location</th>
                        <th>Students</th>
                        <th>Wallet Balance</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td>
                            <div class="font-semibold text-slate-900 dark:text-slate-100 text-sm">{{ $admin->institute_name ?? $admin->name }}</div>
                            <div class="text-xs text-slate-400 dark:text-slate-500 font-mono mt-0.5">ID: RISE-{{ str_pad($admin->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </td>
                        <td>
                            <div class="font-medium text-slate-800 dark:text-slate-200 text-sm">{{ $admin->name }}</div>
                            <div class="text-xs text-slate-400 dark:text-slate-500">{{ $admin->email }} &bull; {{ $admin->phone_number }}</div>
                        </td>
                        <td class="text-slate-600 dark:text-slate-300 text-sm">
                            {{ $admin->city ?? 'N/A' }}{{ $admin->district ? ', ' . $admin->district : '' }}
                        </td>
                        <td class="font-semibold text-slate-800 dark:text-slate-200 font-mono text-sm">
                            {{ $admin->students_count }}
                        </td>
                        <td>
                            <span class="font-bold font-mono text-sm {{ $admin->wallet_balance > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-slate-500' }}">
                                ₹{{ number_format($admin->wallet_balance, 2) }}
                            </span>
                        </td>
                        <td>
                            @if($admin->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="text-right space-x-1.5 whitespace-nowrap">
                            <a href="{{ route('admin_management.show', $admin->id) }}" class="p-1.5 text-slate-500 hover:text-brand-500 dark:text-slate-400 dark:hover:text-brand-400 text-sm transition" title="View & Wallet">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                            <a href="{{ route('admin_management.edit', $admin->id) }}" class="p-1.5 text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 text-sm transition" title="Edit">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <a href="{{ route('certificates.admin_certificate', $admin->id) }}" target="_blank" class="p-1.5 text-slate-500 hover:text-purple-600 dark:text-slate-400 dark:hover:text-purple-400 text-sm transition" title="Authorization Certificate">
                                <i class="fa-solid fa-certificate"></i>
                            </a>
                            <form action="{{ route('admin_management.toggle_status', $admin->id) }}" method="POST" class="inline" onsubmit="return confirm('Toggle center status?');">
                                @csrf
                                <button type="submit" class="p-1.5 {{ $admin->status === 'active' ? 'text-amber-500 hover:text-amber-600 dark:text-amber-400' : 'text-emerald-500 hover:text-emerald-600 dark:text-emerald-400' }} text-sm transition" title="{{ $admin->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                    <i class="fa-solid fa-power-off"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400 dark:text-slate-500 text-sm">
                            <i class="fa-solid fa-building-circle-xmark text-3xl mb-2 text-slate-300 dark:text-slate-600 block"></i>
                            No center admins found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admins->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#182235]/40 flex items-center justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Showing page {{ $admins->currentPage() }} of {{ $admins->lastPage() }}</span>
            <div>
                {{ $admins->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
