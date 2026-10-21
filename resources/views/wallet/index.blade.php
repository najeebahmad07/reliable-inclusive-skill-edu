@extends('layouts.app')

@section('title', 'Wallet & Transactions')
@section('breadcrumb', 'Finance / Wallet')

@section('content')
<div class="space-y-4">
    <!-- Balance & Top Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Balance Card -->
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white p-5 rounded-xl border border-slate-700/80 shadow-md flex flex-col justify-between">
            <div>
                <span class="text-xs font-medium text-slate-400 uppercase tracking-wider block">
                    {{ Auth::user()->isSuperAdmin() ? 'Total System Balance' : 'Current Wallet Balance' }}
                </span>
                <div class="text-3xl font-extrabold text-white mt-2 font-mono tracking-tight">₹{{ number_format(Auth::user()->wallet_balance, 2) }}</div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-700/60 flex items-center justify-between">
                <span class="text-xs text-slate-400">Currency: INR (₹)</span>
                @if(!Auth::user()->isSuperAdmin())
                <a href="{{ route('wallet.recharge') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-xs transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Recharge Now</span>
                </a>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 md:col-span-2 flex flex-col justify-between shadow-xs">
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Wallet Guidelines & Info</h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
                    Student registrations require an approval fee deducted automatically from your center's wallet. Keep your balance updated to prevent any enrollment delays.
                </p>
            </div>
            <div class="flex items-center space-x-2 pt-3 text-sm">
                @if(!Auth::user()->isSuperAdmin())
                <a href="{{ route('wallet.failed_transactions') }}" class="text-rose-600 dark:text-rose-400 hover:underline font-semibold flex items-center space-x-1.5">
                    <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                    <span>Payment deducted but not credited? Report here &rarr;</span>
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('wallet.index') }}" class="flex flex-wrap items-center gap-3">
            @if(Auth::user()->isSuperAdmin())
            <div class="w-56">
                <select name="admin_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">All Centers</option>
                    @foreach($centers as $c)
                    <option value="{{ $c->id }}" {{ request('admin_id') == $c->id ? 'selected' : '' }}>{{ $c->institute_name ?? $c->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="w-44">
                <select name="type" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">All Types (Credit/Debit)</option>
                    <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credit (+)</option>
                    <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>Debit (-)</option>
                </select>
            </div>

            <div class="w-48">
                <select name="transaction_type" class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">All Categories</option>
                    <option value="recharge" {{ request('transaction_type') === 'recharge' ? 'selected' : '' }}>Recharge</option>
                    <option value="approval_fee" {{ request('transaction_type') === 'approval_fee' ? 'selected' : '' }}>Approval Fee</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 dark:bg-brand-600 dark:hover:bg-brand-700 text-white rounded-lg text-sm font-medium transition">Filter</button>
            @if(request()->hasAny(['admin_id', 'type', 'transaction_type']))
            <a href="{{ route('wallet.index') }}" class="px-3.5 py-2 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg text-sm font-medium transition">Reset</a>
            @endif
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-saas">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-[#182235]/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                        <th class="w-12 text-center">#</th>
                        @if(Auth::user()->isSuperAdmin())
                        <th>Center</th>
                        @endif
                        <th>Date & Time</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80">
                    @forelse($transactions as $index => $tx)
                    <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="text-center text-slate-400 dark:text-slate-500 font-mono text-xs">{{ $transactions->firstItem() + $index }}</td>
                        @if(Auth::user()->isSuperAdmin())
                        <td class="font-medium text-slate-900 dark:text-slate-100 text-sm truncate max-w-[180px]">
                            {{ $tx->admin->institute_name ?? 'Center' }}
                        </td>
                        @endif
                        <td class="text-slate-600 dark:text-slate-300 text-sm whitespace-nowrap font-mono">
                            {{ $tx->created_at ? $tx->created_at->format('d M Y, h:i A') : 'N/A' }}
                        </td>
                        <td class="text-slate-800 dark:text-slate-200 text-sm">
                            <div>{{ $tx->description }}</div>
                            @if($tx->razorpay_payment_id)
                            <div class="text-xs text-slate-400 dark:text-slate-500 font-mono mt-0.5">Payment ID: {{ $tx->razorpay_payment_id }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $tx->type === 'credit' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800' }}">
                                {{ $tx->type }}
                            </span>
                        </td>
                        <td class="font-mono font-bold text-sm {{ $tx->type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Success
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-slate-400 dark:text-slate-500 text-sm">No transactions recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-[#182235]/40 flex items-center justify-between">
            <span class="text-xs text-slate-500 dark:text-slate-400">Showing page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}</span>
            <div>
                {{ $transactions->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
