@extends('layouts.app')

@section('title', 'Authorized Center Certificates')
@section('breadcrumb', 'Certificates / Center Authorizations')

@section('content')
<div class="space-y-4">
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Authorized Center Certificates</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">View and print official authorization certificates for all partner centers</p>
        </div>
    </div>

    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th>Center Code</th>
                        <th>Institute Name</th>
                        <th>In-Charge</th>
                        <th>City / State</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($centers as $c)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="font-mono text-slate-800 dark:text-slate-200 font-bold text-xs">RISE-{{ str_pad($c->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="font-semibold text-slate-900 dark:text-slate-100 text-sm">{{ $c->institute_name }}</td>
                        <td class="text-slate-600 dark:text-slate-300 text-sm">{{ $c->name }}</td>
                        <td class="text-slate-500 dark:text-slate-400 text-xs">{{ $c->city }}, {{ $c->institute_state }}</td>
                        <td>
                            @if($c->status === 'active')
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
                        <td class="text-right">
                            <a href="{{ route('certificates.admin_certificate', $c->id) }}" target="_blank" class="px-3 py-1.5 bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/40 dark:hover:bg-purple-900/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800 rounded-lg text-xs font-semibold inline-flex items-center space-x-1.5 transition">
                                <i class="fa-solid fa-certificate text-xs"></i>
                                <span>View Cert</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-slate-400 dark:text-slate-500 text-sm">No centers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($centers->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#182235]/40 flex items-center justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Showing page {{ $centers->currentPage() }} of {{ $centers->lastPage() }}</span>
            <div>
                {{ $centers->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
