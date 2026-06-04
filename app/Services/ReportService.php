<?php

namespace App\Services;

use App\Exceptions\PostAlreadyReportedException;
use App\Models\Report;
use App\Models\User;
use App\Traits\HasQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ReportService
{
    use HasQuery;

    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new report.
     * Throws exception if user already reported this target.
     */
    public function createReport(string $type, string $targetId, User $user, array $data): Report
    {
        $existing = Report::where('type', $type)
            ->where('target_id', $targetId)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            throw new PostAlreadyReportedException();
        }

        $report = Report::create([
            'type' => $type,
            'target_id' => $targetId,
            'user_id' => $user->id,
            'reason' => $data['reason'],
            'additional_info' => $data['additional_info'] ?? null,
            'status' => 'pending',
        ]);

        $this->notificationService->notifyNewReport($report, $user);

        return $report;
    }

    /**
     * Get reports list with filters and pagination (admin).
     */
    public function getReports(Request $request): LengthAwarePaginator
    {
        $query = Report::query()->with(['reporter.profile']);

        $query = $this->applyFilter($query, $request, ['status', 'type', 'reason']);
        $query = $this->applySort($query, $request);

        return $this->paginate($query, $request);
    }

    /**
     * Get a single report with relations loaded.
     */
    public function findById(Report $report): Report
    {
        return $report->load(['reporter.profile']);
    }

    /**
     * Update report status (admin action).
     */
    public function updateStatus(Report $report, array $data, User $admin): Report
    {
        $report->update([
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? $report->admin_notes,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $report->load(['reporter.profile']);
    }
}
