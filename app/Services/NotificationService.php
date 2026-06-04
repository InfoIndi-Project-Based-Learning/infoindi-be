<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Broadcast an admin notification to all users.
     */
    public function broadcastAdmin(string $title, string $message, ?string $senderId = null): Notification
    {
        return DB::transaction(function () use ($title, $message, $senderId) {
            $notification = Notification::create([
                'type' => 'admin',
                'title' => $title,
                'message' => $message,
                'sender_id' => $senderId,
            ]);

            // Get all user IDs
            $userIds = User::pluck('id')->toArray();
            $notification->users()->attach($userIds);

            return $notification;
        });
    }

    /**
     * Notify followers when a user creates a new post.
     */
    public function notifyNewPost(Post $post): ?Notification
    {
        $sender = $post->user;
        $followers = $sender->followers()->pluck('follower_id')->toArray();

        if (empty($followers)) {
            return null;
        }

        return DB::transaction(function () use ($post, $sender, $followers) {
            $notification = Notification::create([
                'type' => 'new_post',
                'title' => 'Postingan Baru',
                'message' => "{$sender->name} mengunggah postingan baru: {$post->post_name}",
                'sender_id' => $sender->id,
                'post_id' => $post->id,
            ]);

            $notification->users()->attach($followers);

            return $notification;
        });
    }

    /**
     * Notify a user when someone follows them.
     */
    public function notifyNewFollower(User $follower, User $followedUser): Notification
    {
        return DB::transaction(function () use ($follower, $followedUser) {
            $notification = Notification::create([
                'type' => 'new_follower',
                'title' => 'Pengikut Baru',
                'message' => "{$follower->name} mulai mengikuti Anda.",
                'sender_id' => $follower->id,
            ]);

            $notification->users()->attach($followedUser->id);

            return $notification;
        });
    }

    /**
     * Notify post owner when someone likes their post.
     */
    public function notifyPostLiked(Post $post, User $liker): ?Notification
    {
        // Don't notify if liking own post
        if ($post->user_id === $liker->id) {
            return null;
        }

        return DB::transaction(function () use ($post, $liker) {
            $notification = Notification::create([
                'type' => 'post_liked',
                'title' => 'Postingan Disukai',
                'message' => "{$liker->name} menyukai postingan Anda: {$post->post_name}",
                'sender_id' => $liker->id,
                'post_id' => $post->id,
            ]);

            $notification->users()->attach($post->user_id);

            return $notification;
        });
    }

    /**
     * Notify post owner when someone comments on their post.
     */
    public function notifyPostCommented(Post $post, User $commenter): ?Notification
    {
        // Don't notify if commenting on own post
        if ($post->user_id === $commenter->id) {
            return null;
        }

        return DB::transaction(function () use ($post, $commenter) {
            $notification = Notification::create([
                'type' => 'post_commented',
                'title' => 'Komentar Baru',
                'message' => "{$commenter->name} mengomentari postingan Anda: {$post->post_name}",
                'sender_id' => $commenter->id,
                'post_id' => $post->id,
            ]);

            $notification->users()->attach($post->user_id);

            return $notification;
        });
    }

    /**
     * Notify admins when a new report is submitted.
     */
    public function notifyNewReport(Report $report, User $reporter): ?Notification
    {
        $admins = User::where('role', 'admin')->pluck('id')->toArray();

        if (empty($admins)) {
            return null;
        }

        return DB::transaction(function () use ($report, $reporter, $admins) {
            $notification = Notification::create([
                'type' => 'admin',
                'title' => 'Laporan Baru Masuk',
                'message' => "Pengguna {$reporter->name} mengirimkan laporan: " . \Illuminate\Support\Str::limit($report->reason, 50),
                'sender_id' => $reporter->id,
                'post_id' => $report->type === 'post' ? $report->target_id : null,
            ]);

            $notification->users()->attach($admins);

            return $notification;
        });
    }
}
