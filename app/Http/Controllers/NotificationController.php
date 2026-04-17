<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** GET /notifications — full list page */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(30);

        return view('notifications.index', compact('notifications'));
    }

    /** GET /notifications/unread-count — lightweight JSON for polling */
    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }

    /** GET /notifications/recent — last 8 unread for the dropdown */
    public function recent()
    {
        $rows = Auth::user()
            ->unreadNotifications()
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->data['title'] ?? '',
                'body'       => $n->data['body'] ?? '',
                'type'       => $n->data['type'] ?? 'system',
                'icon'       => $n->data['icon'] ?? '🔔',
                'url'        => $n->data['url'] ?? null,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json(['notifications' => $rows]);
    }

    /** POST /notifications/{id}/read — mark one as read */
    public function markRead(string $id)
    {
        $n = Auth::user()->notifications()->findOrFail($id);
        $n->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        $url = $n->data['url'] ?? route('notifications.index');
        return redirect($url);
    }

    /** POST /notifications/read-all — mark all as read */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }
}
