<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\UnauthorizedActionException;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Report\StoreReportRequest;
use App\Http\Requests\Report\UpdateReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\Post;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends BaseApiController
{
    private ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * User reports a post.
     */
    public function store(StoreReportRequest $request, Post $post)
    {
        $user = auth('api')->user();
        $report = $this->reportService->createReport('post', $post->id, $user, $request->validated());

        return $this->created(new ReportResource($report), 'Report submitted successfully.');
    }

    /**
     * Admin: list all reports with filters.
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $reports = $this->reportService->getReports($request);
        return $this->paginated($reports, ReportResource::class, 'Reports fetched successfully.');
    }

    /**
     * Admin: view report detail.
     */
    public function show(Report $report)
    {
        $user = auth('api')->user();

        $report = $this->reportService->findById($report);
        return $this->success(new ReportResource($report), 'Report fetched successfully.');
    }

    /**
     * Admin: update report status.
     */
    public function update(UpdateReportRequest $request, Report $report)
    {
        $user = auth('api')->user();

        $report = $this->reportService->updateStatus($report, $request->validated(), $user);
        return $this->success(new ReportResource($report), 'Report updated successfully.');
    }
}
