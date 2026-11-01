<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportReview extends Model
{
    // Laporan yang ditinjau
    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_id');
    }

    // Relawan yang melakukan peninjauan
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    
    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }
}