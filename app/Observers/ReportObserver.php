<?php

namespace App\Observers;

use App\Models\Report;
use App\Models\ReportStatusLog;
use Illuminate\Support\Facades\Auth;

class ReportObserver
{
    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
        {
            if (!$report->wasChanged('status')) {
                return;
            }

            $previous = $report->getPrevious();

            $oldStatus = $previous['status'] ?? null;
            $newStatus = $report->status;

            if ($oldStatus === $newStatus) {
                return;
            }

            $log = new ReportStatusLog();

            $log->changed_by = Auth::id();
            $log->old_status = $oldStatus;
            $log->new_status = $newStatus;

            $report->statusLogs()->save($log);
        }

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "restored" event.
     */
    public function restored(Report $report): void
    {
        //
    }

    /**
     * Handle the Report "force deleted" event.
     */
    public function forceDeleted(Report $report): void
    {
        //
    }
}
