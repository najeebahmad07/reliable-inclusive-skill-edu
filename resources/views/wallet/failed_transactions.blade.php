@extends('layouts.app')

@section('title', 'Report Failed Transaction')
@section('breadcrumb', 'Finance / Failed Transactions')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Report Failed Transaction</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">If your account was debited but the wallet was not updated, report here with payment proof</p>
        </div>
        <a href="{{ route('wallet.index') }}" class="px-3.5 py-2 text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back to Wallet</span>
        </a>
    </div>

    <!-- Report Form Card -->
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center space-x-2">
            <i class="fa-solid fa-file-invoice-dollar text-brand-500 text-base"></i>
            <span>Submit Transaction Details</span>
        </h2>

        <form method="POST" action="{{ route('wallet.store_failed_transaction') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Transaction Date *</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Transaction / UTR / Reference No *</label>
                    <input type="text" name="transaction_number" value="{{ old('transaction_number') }}" required placeholder="e.g. 123456789012"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Amount Debited (₹) *</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required min="1" step="0.01" placeholder="e.g. 1000"
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Description / Remarks</label>
                    <input type="text" name="description" value="{{ old('description') }}" placeholder="Bank name, payment app used, etc."
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Payment Proof Screenshot *</label>
                    <input type="file" name="screenshot" required accept="image/*,application/pdf"
                           class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
                </div>
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="px-5 py-2.5 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-bold shadow-sm transition">
                    Submit for Verification
                </button>
            </div>
        </form>
    </div>

    <!-- Prior Requests History -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="px-5 py-3.5 bg-slate-50/75 dark:bg-[#182235]/60 border-b border-slate-200 dark:border-slate-800">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">My Submitted Requests</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-[#182235]/40 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th>Date</th>
                        <th>Tx / Reference No</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Proof</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($requests as $req)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="font-mono text-xs text-slate-600 dark:text-slate-300">{{ $req->transaction_date ? $req->transaction_date->format('d M Y') : 'N/A' }}</td>
                        <td class="font-mono font-bold text-slate-900 dark:text-slate-100 text-sm">{{ $req->transaction_number }}</td>
                        <td class="font-mono font-bold text-sm text-slate-900 dark:text-white">₹{{ number_format($req->amount, 2) }}</td>
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
                        <td>
                            @if($req->screenshot)
                            <a href="{{ asset('uploads/failed_transactions/' . $req->screenshot) }}" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline text-xs font-semibold">View Proof</a>
                            @endif
                        </td>
                        <td class="text-slate-600 dark:text-slate-300 text-xs">{{ $req->admin_reason ?? $req->description ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center text-slate-400 dark:text-slate-500 text-sm">No failed transaction reports found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
