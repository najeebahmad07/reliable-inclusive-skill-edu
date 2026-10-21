@extends('layouts.app')

@section('title', 'Recharge Wallet')
@section('breadcrumb', 'Wallet / Online Recharge')

@section('content')
<div class="max-w-md mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Recharge Wallet</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Current Balance: <strong class="text-emerald-600 dark:text-emerald-400 font-mono">₹{{ number_format($user->wallet_balance, 2) }}</strong></p>
        </div>
        <a href="{{ route('wallet.index') }}" class="px-3.5 py-2 text-slate-600 dark:text-slate-300 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 rounded-lg text-sm font-medium transition flex items-center space-x-1.5">
            <i class="fa-solid fa-arrow-left text-xs"></i>
            <span>Back</span>
        </a>
    </div>

    <!-- Recharge Form Card -->
    <div class="bg-white dark:bg-[#151c2c] p-6 rounded-xl border border-slate-200 dark:border-slate-800 space-y-4 shadow-xs">
        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Select Quick Amount (₹)</label>
            <div class="grid grid-cols-4 gap-2.5 mb-4">
                <button type="button" onclick="setAmount(500)" class="py-2 px-2 bg-slate-50 dark:bg-[#182235] hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-100 transition">₹500</button>
                <button type="button" onclick="setAmount(1000)" class="py-2 px-2 bg-slate-50 dark:bg-[#182235] hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-100 transition">₹1,000</button>
                <button type="button" onclick="setAmount(2000)" class="py-2 px-2 bg-slate-50 dark:bg-[#182235] hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-100 transition">₹2,000</button>
                <button type="button" onclick="setAmount(5000)" class="py-2 px-2 bg-slate-50 dark:bg-[#182235] hover:bg-brand-50 hover:text-brand-600 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-800 dark:text-slate-100 transition">₹5,000</button>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Or Enter Custom Amount (₹) *</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-bold text-slate-400 dark:text-slate-500 text-sm">₹</span>
                <input type="number" id="rechargeAmount" min="{{ $minAmount }}" step="1" value="1000"
                       class="w-full pl-8 pr-3 py-2.5 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-base font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-brand-500 font-mono">
            </div>
            <span class="text-xs text-slate-400 dark:text-slate-500 mt-1 block">Minimum recharge amount is ₹{{ $minAmount }}</span>
        </div>

        <!-- Payment Gateway Info -->
        <div class="p-3.5 bg-slate-50 dark:bg-[#182235]/60 rounded-xl border border-slate-200 dark:border-slate-700/70 text-xs text-slate-600 dark:text-slate-300 flex items-center space-x-3">
            <i class="fa-solid fa-shield-halved text-emerald-500 text-lg"></i>
            <div>
                <div class="font-bold text-slate-900 dark:text-white text-xs">Secure Payment Gateway</div>
                <div class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Supports UPI, NetBanking, Debit/Credit Cards via Razorpay</div>
            </div>
        </div>

        <!-- Hidden Verification Form for Razorpay Callback -->
        <form id="verifyPaymentForm" method="POST" action="{{ route('wallet.verify_payment') }}" class="hidden">
            @csrf
            <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
            <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
            <input type="hidden" name="amount" id="verified_amount">
        </form>

        <button type="button" id="payBtn" onclick="initiatePayment()"
                class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-sm shadow transition flex items-center justify-center space-x-2">
            <i class="fa-solid fa-lock text-xs"></i>
            <span>Proceed to Secure Pay</span>
        </button>
    </div>
</div>

@push('scripts')
<!-- Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    function setAmount(val) {
        document.getElementById('rechargeAmount').value = val;
    }

    function initiatePayment() {
        const amount = document.getElementById('rechargeAmount').value;
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Processing...</span>';

        fetch('{{ route("wallet.create_order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-lock text-xs"></i> <span>Proceed to Secure Pay</span>';

            if (!data.success) {
                alert('Failed to initiate order. Please try again.');
                return;
            }

            // Options for Razorpay modal
            const options = {
                "key": data.key,
                "amount": data.amount,
                "currency": "INR",
                "name": "{{ config('rise.app_name', 'RISE') }}",
                "description": "Wallet Recharge",
                "order_id": data.order_id,
                "handler": function (response) {
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    document.getElementById('razorpay_order_id').value = response.razorpay_order_id || data.order_id;
                    document.getElementById('verified_amount').value = amount;
                    document.getElementById('verifyPaymentForm').submit();
                },
                "prefill": {
                    "name": "{{ $user->name }}",
                    "email": "{{ $user->email }}",
                    "contact": "{{ $user->phone_number }}"
                },
                "theme": {
                    "color": "#4e73df"
                }
            };

            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function (response){
                alert('Payment Failed: ' + response.error.description);
            });
            rzp.open();
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-lock text-xs"></i> <span>Proceed to Secure Pay</span>';
            console.error(err);
            alert('Something went wrong connecting to payment gateway.');
        });
    }
</script>
@endpush
@endsection
