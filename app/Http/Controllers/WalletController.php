<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\WalletTransaction;
use App\Models\FailedTransaction;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::user();

        $query = WalletTransaction::with('admin');

        if (!$user->isSuperAdmin()) {
            $query->where('admin_id', $user->id);
        } elseif ($request->filled('admin_id')) {
            $query->where('admin_id', $request->admin_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        $transactions = $query->latest('id')->paginate(20)->withQueryString();
        $centers = $user->isSuperAdmin() ? Admin::where('role', 'admin')->orderBy('institute_name')->get() : collect();

        return view('wallet.index', compact('transactions', 'centers', 'user'));
    }

    public function recharge()
    {
        /** @var Admin $user */
        $user = Auth::user();
        $minAmount = config('rise.min_recharge_amount', 100);
        $razorpayKey = config('rise.razorpay.key_id');

        return view('wallet.recharge', compact('user', 'minAmount', 'razorpayKey'));
    }

    public function createOrder(Request $request)
    {
        $minAmount = config('rise.min_recharge_amount', 100);
        $request->validate([
            'amount' => "required|numeric|min:{$minAmount}",
        ]);

        $amountInPaise = (int) ($request->amount * 100);
        $keyId = config('rise.razorpay.key_id');
        $keySecret = config('rise.razorpay.key_secret');

        // Create Razorpay Order via cURL
        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_USERPWD, $keyId . ':' . $keySecret);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'amount' => $amountInPaise,
            'currency' => 'INR',
            'receipt' => 'rcpt_' . time(),
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $orderData = json_decode($response, true);

        if ($httpCode === 200 && isset($orderData['id'])) {
            return response()->json([
                'success' => true,
                'order_id' => $orderData['id'],
                'amount' => $amountInPaise,
                'key' => $keyId,
            ]);
        }

        // Fallback for testing/offline
        $mockOrderId = 'order_mock_' . time();
        return response()->json([
            'success' => true,
            'order_id' => $mockOrderId,
            'amount' => $amountInPaise,
            'key' => $keyId,
            'is_mock' => true,
        ]);
    }

    public function verifyPayment(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::user();

        $request->validate([
            'razorpay_payment_id' => 'required',
            'amount' => 'required|numeric',
        ]);

        $amount = (float) $request->amount;
        $paymentId = $request->razorpay_payment_id;
        $orderId = $request->razorpay_order_id ?? 'N/A';

        DB::transaction(function () use ($user, $amount, $paymentId, $orderId) {
            $user->increment('wallet_balance', $amount);

            WalletTransaction::create([
                'admin_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'transaction_type' => 'recharge',
                'razorpay_payment_id' => $paymentId,
                'razorpay_order_id' => $orderId,
                'description' => "Online Wallet Recharge (Payment ID: {$paymentId})",
                'status' => 'success',
                'created_at' => now(),
            ]);
        });

        return redirect()->route('wallet.index')->with('success', "Wallet recharged successfully with ₹" . number_format($amount, 2));
    }

    // Failed transactions
    public function failedTransactions()
    {
        /** @var Admin $user */
        $user = Auth::user();
        $requests = FailedTransaction::where('admin_id', $user->id)->latest()->paginate(15);
        return view('wallet.failed_transactions', compact('requests'));
    }

    public function storeFailedTransaction(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::user();

        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_number' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'screenshot' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
        ]);

        $filename = '';
        if ($request->hasFile('screenshot')) {
            $file = $request->file('screenshot');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads/failed_transactions');
            if (!file_exists($dest)) mkdir($dest, 0777, true);
            $file->move($dest, $filename);
        }

        FailedTransaction::create([
            'admin_id' => $user->id,
            'transaction_date' => $validated['transaction_date'],
            'transaction_number' => $validated['transaction_number'],
            'amount' => $validated['amount'],
            'description' => $validated['description'] ?? null,
            'screenshot' => $filename,
            'status' => 'Pending',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Failed transaction report submitted. Super Admin will review it shortly.');
    }

    public function failedTransactionRequests()
    {
        $requests = FailedTransaction::with('admin')->latest()->paginate(20);
        return view('wallet.failed_requests_super', compact('requests'));
    }

    public function reviewFailedTransaction(Request $request, $id)
    {
        $failedTx = FailedTransaction::with('admin')->findOrFail($id);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_reason' => 'nullable|string',
        ]);

        /** @var Admin $superAdmin */
        $superAdmin = Auth::user();

        if ($request->action === 'approve') {
            DB::transaction(function () use ($failedTx, $superAdmin, $request) {
                $failedTx->admin->increment('wallet_balance', $failedTx->amount);

                WalletTransaction::create([
                    'admin_id' => $failedTx->admin_id,
                    'amount' => $failedTx->amount,
                    'type' => 'credit',
                    'transaction_type' => 'recharge',
                    'description' => "Failed Transaction Approved (Tx #{$failedTx->transaction_number})",
                    'status' => 'success',
                    'created_at' => now(),
                ]);

                $failedTx->update([
                    'status' => 'Approved',
                    'admin_reason' => $request->admin_reason,
                    'reviewed_by' => $superAdmin->id,
                    'reviewed_at' => now(),
                ]);
            });

            return back()->with('success', "Transaction #{$failedTx->transaction_number} approved and ₹{$failedTx->amount} credited to center.");
        } else {
            $failedTx->update([
                'status' => 'Rejected',
                'admin_reason' => $request->admin_reason,
                'reviewed_by' => $superAdmin->id,
                'reviewed_at' => now(),
            ]);

            return back()->with('info', "Transaction #{$failedTx->transaction_number} marked as Rejected.");
        }
    }
}

