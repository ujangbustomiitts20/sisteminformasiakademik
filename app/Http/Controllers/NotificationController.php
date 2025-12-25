<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications
     */
    public function recent()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        if ($notification->link) {
            return redirect($notification->link);
        }
        
        return redirect()->route('notifications.index');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Delete all read notifications
     */
    public function clearRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', true)
            ->delete();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi yang sudah dibaca telah dihapus.');
    }

    /**
     * Send notification to user
     */
    public static function send($userId, $title, $message, $type = 'info', $link = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public static function sendToMany(array $userIds, $title, $message, $type = 'info', $link = null)
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'link' => $link,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        Notification::insert($notifications);
    }

    /**
     * Send notification to users by role
     */
    public static function sendToRole($role, $title, $message, $type = 'info', $link = null)
    {
        $userIds = \App\Models\User::where('role', $role)
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();
        
        self::sendToMany($userIds, $title, $message, $type, $link);
    }
}
