<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PlacementStudent;
use App\Models\Admin;

class PlacementStudentController extends Controller
{
    public function index()
    {
        /** @var Admin $user */
        $user = Auth::user();

        $query = PlacementStudent::with('admin');
        if (!$user->isSuperAdmin()) {
            $query->where('admin_id', $user->id);
        }

        $placements = $query->latest('id')->paginate(15);
        return view('placements.index', compact('placements'));
    }

    public function store(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::user();

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'placement_location' => 'required|string|max:255',
            'center_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $dest = public_path('uploads/placement_students');
            if (!file_exists($dest)) mkdir($dest, 0777, true);
            $file->move($dest, $imageName);
        }

        PlacementStudent::create([
            'admin_id' => $user->id,
            'center_name' => $validated['center_name'] ?? $user->institute_name ?? 'RISE Center',
            'student_name' => $validated['student_name'],
            'placement_location' => $validated['placement_location'],
            'images' => $imageName,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Placement record added successfully.');
    }

    public function destroy($id)
    {
        $placement = PlacementStudent::findOrFail($id);
        /** @var Admin $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin() && $placement->admin_id !== $user->id) {
            abort(403);
        }

        $placement->delete();
        return back()->with('success', 'Placement record deleted.');
    }
}

