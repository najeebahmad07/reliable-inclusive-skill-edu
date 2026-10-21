@extends('layouts.app')

@section('title', 'Failed Transaction Review')
@section('breadcrumb', 'Finance / Failed Transaction Requests')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Failed Transaction Claims</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Review claims submitted by centers and verify bank payment receipts</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th>Center</th>
                        <th>Tx Date</th>
                        <th>Reference / UTR</th>
                        <th>Amount</th>
                        <th>Proof</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($requests as $req)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td>
                            <div class="font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $req->admin->institute_name ?? 'Center' }}</div>
                            <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $req->admin->name ?? '' }} ({{ $req->admin->phone_number ?? '' }})</div>
                        </td>
                        <td class="font-mono text-xs whitespace-nowrap text-slate-600 dark:text-slate-300">{{ $req->transaction_date ? $req->transaction_date->format('d M Y') : 'N/A' }}</td>
                        <td class="font-mono font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $req->transaction_number }}</td>
                        <td class="font-mono font-bold text-sm text-emerald-600 dark:text-emerald-400">₹{{ number_format($req->amount, 2) }}</td>
                        <td>
                            @if($req->screenshot)
                            <a href="{{ asset('uploads/failed_transactions/' . $req->screenshot) }}" target="_blank" class="px-2.5 py-1 rounded-md bg-brand-50 hover:bg-brand-100 dark:bg-brand-950/40 dark:hover:bg-brand-900/40 text-brand-700 dark:text-brand-300 border border-brand-200 dark:border-brand-800 text-xs font-semibold inline-flex items-center space-x-1.5 transition">
                                <i class="fa-solid fa-file-image text-xs"></i>
                                <span>View Receipt</span>
                            </a>
                            @else
                            <span class="text-slate-400 dark:text-slate-500 text-xs">No file</span>
                            @endif
                        </td>
                        <td>
                            @if($req->status === 'Approved')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Approved
                            </span>
                            @elseif($req->status === 'Pending')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                Pending Review
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                Rejected
                            </span>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap space-x-1.5">
                            @if($req->status === 'Pending')
                            <form action="{{ route('wallet.review_failed_transaction', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Approve this transaction? ₹{{ $req->amount }} will be credited to the center wallet.');">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="px-2.5 py-1 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs transition">
                                    Approve & Credit
                                </button>
                            </form>
                            <form action="{{ route('wallet.review_failed_transaction', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Reject this transaction request?');">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="px-2.5 py-1 rounded-md bg-rose-100 hover:bg-rose-200 dark:bg-rose-950/40 dark:hover:bg-rose-900/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-medium transition">
                                    Reject
                                </button>
                            </form>
                            @else
                            <span class="text-slate-400 dark:text-slate-500 text-xs">Reviewed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center text-slate-400 dark:text-slate-500 text-sm">No failed transaction claims to review.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="px-5 py-3.5 bg-slate-50/50 dark:bg-[#182235]/40 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Showing page {{ $requests->currentPage() }} of {{ $requests->lastPage() }}</span>
            <div>
                {{ $requests->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
