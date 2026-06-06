<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;

class HealthController extends BaseApiController
{
    public function index()
    {
        $data = [
            'App Name' => config('app.name'),
            'App Version' => config('app.version'),
            'App Environment' => config('app.env'),
            'App Debug' => config('app.debug'),
        ];
        return $this->success($data, 'Success get data', 200);
    }

    public function statistics()
    {
        $usersCount = \App\Models\User::count();
        $postsCount = \App\Models\Post::count();
        $categoriesCount = \App\Models\Category::count();

        $data = [
            'users' => $usersCount,
            'posts' => $postsCount,
            'categories' => $categoriesCount,
        ];

        return $this->success($data, 'Success get statistics data', 200);
    }
}