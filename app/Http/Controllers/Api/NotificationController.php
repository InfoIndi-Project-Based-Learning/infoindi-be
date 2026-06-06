<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();
        
        $notifications = $user->notifications()
            ->with('sender.profile')
            ->latest()
            ->paginate($request->get('per_page', 15));

        return $this->success(NotificationResource::collection($notifications)->response()->getData(true), 'Notifications fetched successfully.');
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount()
    {
        $count = auth('api')->user()->notifications()
            ->wherePivot('is_read', false)
            ->count();

        return $this->success(['unread_count' => $count], 'Unread count fetched successfully.');
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        $user = auth('api')->user();
        
        $user->notifications()->updateExistingPivot($notification->id, [
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->success(null, 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $user = auth('api')->user();
        
        $user->notifications()->wherePivot('is_read', false)->updateExistingPivot($user->notifications()->pluck('notifications.id'), [
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->success(null, 'All notifications marked as read.');
    }
}
