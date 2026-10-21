@extends('layouts.app')

@section('title', 'Notifications')
@section('breadcrumb', 'Communication / Notifications')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
    <!-- Header -->
    <div class="bg-white dark:bg-[#151c2c] p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                {{ Auth::user()->isSuperAdmin() ? 'Certificate & Approval Requests' : 'Center Notifications' }}
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">System generated alerts, certificate issuance requests, and status updates</p>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs divide-y divide-slate-100 dark:divide-slate-800/80">
        @forelse($notifications as $notif)
        <div class="p-4 hover:bg-slate-50/75 dark:hover:bg-slate-800/30 transition flex items-start space-x-3.5">
            <div class="w-8 h-8 rounded-full bg-brand-50 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/60 flex items-center justify-center text-xs flex-shrink-0 mt-0.5">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm font-semibold text-slate-900 dark:text-white">
                        {{ $notif->message }}
                    </span>
                    <span class="text-xs text-slate-400 dark:text-slate-500 font-mono whitespace-nowrap">
                        {{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}
                    </span>
                </div>

                @if($notif->student)
                <div class="mt-2 flex items-center space-x-3 text-xs">
                    <span class="text-slate-500 dark:text-slate-400">
                        Student: <strong class="text-slate-700 dark:text-slate-200">{{ $notif->student->full_name }}</strong>
                    </span>
                    <a href="{{ route('students.show', $notif->student_id) }}" class="text-brand-600 dark:text-brand-400 hover:underline font-semibold">
                        View Student Profile &rarr;
                    </a>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-slate-400 dark:text-slate-500 text-sm">
            <i class="fa-regular fa-bell-slash text-3xl text-slate-300 dark:text-slate-600 mb-2 block"></i>
            No notifications available.
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="p-4 bg-white dark:bg-[#151c2c] rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
