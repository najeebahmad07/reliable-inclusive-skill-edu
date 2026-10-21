<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Program;
use App\Models\WalletTransaction;

class AdminManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Admin::withCount('students')->where('role', 'admin');

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('institute_name', 'like', "%{$s}%")
                  ->orWhere('phone_number', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $admins = $query->latest('id')->paginate(15)->withQueryString();

        return view('admin_management.index', compact('admins'));
    }

    public function create()
    {
        $programs = Program::orderBy('program_name')->get();
        return view('admin_management.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:20',
            'institute_name' => 'required|string|max:255',
            'institute_address' => 'nullable|string',
            'institute_state' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'institute_pin' => 'nullable|string|max:20',
            'total_pc' => 'nullable|integer',
            'total_staffs' => 'nullable|integer',
            'practical_labs' => 'nullable|integer',
            'theory_rooms' => 'nullable|integer',
            'initial_balance' => 'nullable|numeric|min:0',
            'assigned_programs' => 'nullable|array',
            'assigned_programs.*' => 'exists:programs,id',
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'] ?? null,
            'institute_name' => $validated['institute_name'],
            'institute_address' => $validated['institute_address'] ?? null,
            'institute_state' => $validated['institute_state'] ?? null,
            'district' => $validated['district'] ?? null,
            'city' => $validated['city'] ?? null,
            'institute_pin' => $validated['institute_pin'] ?? null,
            'total_pc' => $validated['total_pc'] ?? 0,
            'total_staffs' => $validated['total_staffs'] ?? 0,
            'practical_labs' => $validated['practical_labs'] ?? 0,
            'theory_rooms' => $validated['theory_rooms'] ?? 0,
            'role' => 'admin',
            'status' => 'active',
            'wallet_balance' => $validated['initial_balance'] ?? 0,
        ]);

        if (!empty($validated['assigned_programs'])) {
            $admin->programs()->sync($validated['assigned_programs']);
        }

        if (!empty($validated['initial_balance']) && $validated['initial_balance'] > 0) {
            WalletTransaction::create([
                'admin_id' => $admin->id,
                'amount' => $validated['initial_balance'],
                'type' => 'credit',
                'transaction_type' => 'recharge',
                'description' => 'Initial Wallet Credit by Super Admin',
                'status' => 'success',
                'created_at' => now(),
            ]);
        }

        return redirect()->route('admin_management.index')->with('success', "Center Admin '{$admin->name}' created successfully.");
    }

    public function show($id)
    {
        $admin = Admin::with(['programs', 'students' => function ($q) {
            $q->latest()->take(10);
        }, 'walletTransactions' => function ($q) {
            $q->latest()->take(10);
        }])->findOrFail($id);

        return view('admin_management.show', compact('admin'));
    }

    public function edit($id)
    {
        $admin = Admin::with('programs')->findOrFail($id);
        $programs = Program::orderBy('program_name')->get();
        return view('admin_management.edit', compact('admin', 'programs'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|string|min:6',
            'phone_number' => 'nullable|string|max:20',
            'institute_name' => 'required|string|max:255',
            'institute_address' => 'nullable|string',
            'institute_state' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'institute_pin' => 'nullable|string|max:20',
            'total_pc' => 'nullable|integer',
            'total_staffs' => 'nullable|integer',
            'practical_labs' => 'nullable|integer',
            'theory_rooms' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'assigned_programs' => 'nullable|array',
            'assigned_programs.*' => 'exists:programs,id',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'institute_name' => $validated['institute_name'],
            'institute_address' => $validated['institute_address'] ?? null,
            'institute_state' => $validated['institute_state'] ?? null,
            'district' => $validated['district'] ?? null,
            'city' => $validated['city'] ?? null,
            'institute_pin' => $validated['institute_pin'] ?? null,
            'total_pc' => $validated['total_pc'] ?? 0,
            'total_staffs' => $validated['total_staffs'] ?? 0,
            'practical_labs' => $validated['practical_labs'] ?? 0,
            'theory_rooms' => $validated['theory_rooms'] ?? 0,
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $admin->update($updateData);

        if (isset($validated['assigned_programs'])) {
            $admin->programs()->sync($validated['assigned_programs']);
        }

        return redirect()->route('admin_management.index')->with('success', "Center '{$admin->institute_name}' updated successfully.");
    }

    public function toggleStatus($id)
    {
        $admin = Admin::findOrFail($id);
        $newStatus = $admin->status === 'active' ? 'inactive' : 'active';
        $admin->update(['status' => $newStatus]);

        return back()->with('success', "Center status changed to {$newStatus}.");
    }

    public function adjustWallet(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
        ]);

        $amount = (float) $validated['amount'];

        if ($validated['type'] === 'debit' && $admin->wallet_balance < $amount) {
            return back()->with('error', 'Cannot debit amount greater than current wallet balance.');
        }

        DB::transaction(function () use ($admin, $amount, $validated) {
            if ($validated['type'] === 'credit') {
                $admin->increment('wallet_balance', $amount);
            } else {
                $admin->decrement('wallet_balance', $amount);
            }

            WalletTransaction::create([
                'admin_id' => $admin->id,
                'amount' => $amount,
                'type' => $validated['type'],
                'transaction_type' => 'recharge',
                'description' => "Admin Adjustment ({$validated['type']}): " . $validated['description'],
                'status' => 'success',
                'created_at' => now(),
            ]);
        });

        return back()->with('success', "Wallet adjusted successfully. New balance: ₹" . number_format($admin->fresh()->wallet_balance, 2));
    }
}

