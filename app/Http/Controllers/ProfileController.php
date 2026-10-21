<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class ProfileController extends Controller
{
    public function profile()
    {
        /** @var Admin $user */
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'phone_number' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:150',
            'institute_name' => 'nullable|string|max:255',
            'institute_address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'institute_state' => 'nullable|string|max:100',
            'institute_pin' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword()
    {
        return view('profile.change_password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        /** @var Admin $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password does not match.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}

