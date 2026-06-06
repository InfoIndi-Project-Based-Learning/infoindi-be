<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends BaseApiController
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function overview(Request $request)
    {
        $overview = $this->dashboardService->getOverview();
        $userGrowth = $this->dashboardService->getUserGrowth();
        $userActivity = $this->dashboardService->getUserActivity();
        $topUsers = $this->dashboardService->getTopUsersByLikes(10);

        return $this->success([
            'counts' => $overview,
            'user_growth' => $userGrowth,
            'user_activity' => $userActivity,
            'top_users' => $topUsers,
        ], 'Dashboard statistics fetched successfully.');
    }
}
