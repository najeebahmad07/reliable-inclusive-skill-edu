@extends('layouts.app')

@section('title', 'Center Notices')
@section('breadcrumb', 'Communication / Center Notices')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Center Notice Board</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Post announcements visible on partner center dashboards</p>
        </div>
    </div>

    <!-- Post Notice Card (Super Admin) -->
    @if(Auth::user()->isSuperAdmin())
    <div class="bg-white dark:bg-[#151c2c] p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs space-y-3">
        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center space-x-2">
            <i class="fa-solid fa-bullhorn text-amber-500 text-base"></i>
            <span>Broadcast New Announcement</span>
        </h2>
        <form method="POST" action="{{ route('notices.center_notices.store') }}" class="space-y-3">
            @csrf
            <div>
                <textarea name="message" rows="3" required placeholder="Type the announcement message for centers..."
                          class="w-full px-3 py-2 bg-slate-50 dark:bg-[#182235] border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-brand-500"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 bg-brand-500 hover:bg-brand-600 text-white rounded-lg text-sm font-semibold shadow-xs transition">
                    Post Notice
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Notices List -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs divide-y divide-slate-100 dark:divide-slate-800/80">
        @forelse($notices as $n)
        <div class="p-4 flex items-start justify-between gap-4 text-sm">
            <div class="flex-1">
                <p class="text-slate-800 dark:text-slate-200 leading-relaxed text-sm">{{ $n->message }}</p>
                <span class="text-xs text-slate-400 dark:text-slate-500 mt-1.5 block">
                    Posted {{ $n->created_at ? $n->created_at->diffForHumans() : '' }} by <strong class="text-slate-600 dark:text-slate-300">{{ $n->creator->name ?? 'Head Office' }}</strong>
                </span>
            </div>
            @if(Auth::user()->isSuperAdmin())
            <form action="{{ route('notices.center_notices.delete', $n->id) }}" method="POST" onsubmit="return confirm('Delete this notice?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-slate-400 hover:text-rose-600 dark:text-slate-500 dark:hover:text-rose-400 text-sm transition" title="Delete">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </form>
            @endif
        </div>
        @empty
        <div class="p-10 text-center text-slate-400 dark:text-slate-500 text-sm">No active notices.</div>
        @endforelse
    </div>
</div>
@endsection
