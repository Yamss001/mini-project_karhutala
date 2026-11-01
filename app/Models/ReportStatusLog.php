<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportStatusLog extends Model
{
    // Laporan yang mengalami perubahan status
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    // Pengguna yang mengubah status
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}