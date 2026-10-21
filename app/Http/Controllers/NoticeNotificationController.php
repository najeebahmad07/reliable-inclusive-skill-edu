<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Message;
use App\Models\PublicMessage;
use App\Models\Admin;

class NoticeNotificationController extends Controller
{
    public function notifications()
    {
        /** @var Admin $user */
        $user = Auth::user();

        $notifications = Notification::with(['from', 'student.program', 'student.course'])
            ->where('to_admin', $user->id)
            ->latest('id')
            ->paginate(20);

        // Mark as read
        Notification::where('to_admin', $user->id)->where('is_read', 0)->update(['is_read' => 1]);

        return view('notices.notifications', compact('notifications'));
    }

    public function centerNotices()
    {
        $notices = Message::with('creator')->latest()->paginate(15);
        return view('notices.center_notices', compact('notices'));
    }

    public function storeCenterNotice(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        Message::create([
            'message' => $request->message,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Center notice posted successfully.');
    }

    public function deleteCenterNotice($id)
    {
        Message::findOrFail($id)->delete();
        return back()->with('success', 'Notice deleted.');
    }

    public function publicNotices()
    {
        $notices = PublicMessage::latest()->paginate(15);
        return view('notices.public_notices', compact('notices'));
    }

    public function storePublicNotice(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        PublicMessage::create([
            'message' => $request->message,
            'status' => 1,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Website notice posted successfully.');
    }

    public function togglePublicNotice($id)
    {
        $notice = PublicMessage::findOrFail($id);
        $notice->update(['status' => !$notice->status]);
        return back()->with('success', 'Notice status updated.');
    }

    public function deletePublicNotice($id)
    {
        PublicMessage::findOrFail($id)->delete();
        return back()->with('success', 'Website notice deleted.');
    }
}

