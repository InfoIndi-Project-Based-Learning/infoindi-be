<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminNotificationController extends BaseApiController
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Broadcast a notification to all users.
     */
    public function broadcast(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        $notification = $this->notificationService->broadcastAdmin(
            $request->title,
            $request->message,
            auth('api')->id()
        );

        return $this->success($notification, 'Pengumuman berhasil dikirim ke seluruh pengguna.');
    }
}
