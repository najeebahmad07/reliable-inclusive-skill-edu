@extends('layouts.app')

@section('title', 'Placement Students')
@section('breadcrumb', 'Students / Placements')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Student Placement Records</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Showcase successfully placed students, hiring companies, and packages</p>
        </div>
        <div>
            <button onclick="document.getElementById('addPlacementModal').classList.remove('hidden')" class="px-3.5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold flex items-center space-x-2 shadow-xs transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add Placement Record</span>
            </button>
        </div>
    </div>

    <!-- Placements Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($placements as $p)
        <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs flex flex-col justify-between">
            <div>
                <div class="h-44 bg-slate-100 dark:bg-slate-800 overflow-hidden relative flex items-center justify-center text-slate-400">
                    @if($p->images && file_exists(public_path('uploads/placement_students/' . $p->images)))
                    <img src="{{ asset('uploads/placement_students/' . $p->images) }}" class="w-full h-full object-cover">
                    @else
                    <i class="fa-solid fa-user-graduate text-4xl text-slate-300 dark:text-slate-600"></i>
                    @endif
                    <div class="absolute bottom-2 right-2 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-semibold px-2 py-0.5 rounded-md">
                        Placed
                    </div>
                </div>

                <div class="p-4">
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm truncate">{{ $p->student_name }}</h2>
                    <div class="text-xs text-brand-600 dark:text-brand-400 font-semibold truncate mt-1 flex items-center">
                        <i class="fa-solid fa-building text-xs mr-1.5"></i>
                        <span>{{ $p->placement_location }}</span>
                    </div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 truncate mt-1">
                        Center: {{ $p->center_name ?? $p->admin->institute_name ?? 'Center' }}
                    </div>
                </div>
            </div>

            <div class="px-4 py-2.5 bg-slate-50/75 dark:bg-[#182235]/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <span class="text-xs text-slate-400 dark:text-slate-500 font-mono">{{ $p->created_at ? $p->created_at->format('d M Y') : '' }}</span>
                <form action="{{ route('placements.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Delete this record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 text-sm transition">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-400 dark:text-slate-500 text-sm bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800">
            No placement student records added yet.
        </div>
        @endforelse
    </div>

    @if($placements->hasPages())
    <div class="p-4 bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        {{ $placements->links() }}
    </div>
    @endif
</div>

<!-- Add Placement Modal -->
<div id="addPlacementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 w-full max-w-md p-5 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider">Add Placement Student</h2>
            <button onclick="document.getElementById('addPlacementModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-base">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('placements.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Student Full Name *</label>
                <input type="text" name="student_name" required placeholder="Student Name"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Company / Placement Location *</label>
                <input type="text" name="placement_location" required placeholder="e.g. TCS / Noida"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Center Name</label>
                <input type="text" name="center_name" value="{{ Auth::user()->institute_name ?? '' }}" placeholder="Institute Name"
                       class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1.5">Student Photograph</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-md file:border-0 file:text-xs file:bg-brand-50 file:text-brand-700 dark:file:bg-brand-900/30 dark:file:text-brand-300">
            </div>
            <div class="flex items-center justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('addPlacementModal').classList.add('hidden')" class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 rounded-lg text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold transition">Save Record</button>
            </div>
        </form>
    </div>
</div>
@endsection
