<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Report extends Model
{
    // Pengguna yang membuat laporan
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    // Wilayah laporan
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    // Catatan peninjauan
    public function reviews(): HasMany
    {
        return $this->hasMany(ReportReview::class, 'report_id');
    }

    // Catatan penerusan
    public function forwardings(): HasMany
    {
        return $this->hasMany(ReportForwarding::class, 'report_id');
    }

    // Riwayat perubahan status
    public function statusLogs(): HasMany
    {
        return $this->hasMany(ReportStatusLog::class, 'report_id');
    }
    
    protected $fillable = [
        'area_id',
        'incident_at',
        'latitude',
        'longitude',
        'condition',
        'description',
        'status',
        'evidence_path',
    ];

    protected function casts(): array
    {
        return [
            'incident_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

}