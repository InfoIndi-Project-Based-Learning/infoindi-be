<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getOverview()
    {
        return [
            'total_users' => User::count(),
            'total_posts' => Post::count(),
            'total_reports' => Report::count(),
            'total_reports_pending' => Report::where('status', 'pending')->count(),
            'active_users' => User::where('is_active', true)->count(),
            'banned_users' => User::where('is_active', false)->count(),
        ];
    }

    public function getUserGrowth()
    {
        // 12 bulan terakhir (SQLite syntax, kalau MySQL bisa pakai DATE_FORMAT)
        // Karena ini mungkin berjalan di SQLite/MySQL, kita pakai query yang cukup universal kalau bisa,
        // tapi Laravel SQLite driver support strftime. MySQL support DATE_FORMAT.
        // Kita bisa ambil semua dan group by di collection untuk aman dari driver, atau pakai raw.
        // Untuk amannya, kita fetch 12 bulan terakhir lalu group by PHP.
        
        $users = User::where('created_at', '>=', now()->subMonths(12))->get();
        
        $growth = $users->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('Y-m'); // grouping by year-month
        })->map(function ($row) {
            return count($row);
        })->map(function ($count, $month) {
            return ['month' => $month, 'count' => $count];
        })->values()->toArray();

        return $growth;
    }

    public function getUserActivity()
    {
        // Gabungan user registered vs user yang buat post per bulan
        $months = [];
        
        // 1. Registered
        $users = User::where('created_at', '>=', now()->subMonths(12))->get();
        foreach ($users as $user) {
            $month = \Carbon\Carbon::parse($user->created_at)->format('Y-m');
            if (!isset($months[$month])) {
                $months[$month] = ['month' => $month, 'registered' => 0, 'posted' => 0];
            }
            $months[$month]['registered']++;
        }

        // 2. Posted (unique users who posted per month)
        // Ambil posts dalam 12 bulan terakhir
        $posts = Post::where('created_at', '>=', now()->subMonths(12))->get();
        
        // Group posts by month, then count unique user_id
        $postsByMonth = $posts->groupBy(function($post) {
            return \Carbon\Carbon::parse($post->created_at)->format('Y-m');
        });

        foreach ($postsByMonth as $month => $monthPosts) {
            $uniqueUsers = $monthPosts->pluck('user_id')->unique()->count();
            if (!isset($months[$month])) {
                $months[$month] = ['month' => $month, 'registered' => 0, 'posted' => 0];
            }
            $months[$month]['posted'] = $uniqueUsers;
        }

        // Sort by month
        ksort($months);

        return array_values($months);
    }

    public function getTopUsersByLikes($limit = 10)
    {
        // Ambil user dan jumlahkan likes dari semua post mereka
        // Kita bisa gunakan withCount untuk efisiensi
        
        $users = User::withCount(['likedPosts as total_likes_given']) // Jika butuh like yg diberikan
            ->with(['posts' => function($query) {
                $query->withCount('likes'); // Count likes for each post
            }, 'profile'])
            ->get();

        $topUsers = $users->map(function ($user) {
            $totalLikesReceived = $user->posts->sum('likes_count');
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->profile?->avatar,
                'total_likes_received' => $totalLikesReceived,
                'posts_count' => $user->posts->count(),
            ];
        })
        ->sortByDesc('total_likes_received')
        ->take($limit)
        ->values();

        return $topUsers;
    }
}
