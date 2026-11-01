<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Report;
use App\Models\ReportReview;
use App\Models\ReportForwarding;
use App\Models\ReportStatusLog;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    ];

    // Laporan yang dibuat oleh pengguna
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    // Catatan peninjauan yang dibuat pengguna
    public function reviews(): HasMany
    {
        return $this->hasMany(ReportReview::class, 'reviewer_id');
    }

    // Penerusan yang dicatat pengguna
    public function forwardings(): HasMany
    {
        return $this->hasMany(ReportForwarding::class, 'coordinator_id');
    }

    // Perubahan status yang dilakukan pengguna
    public function statusChanges(): HasMany
    {
        return $this->hasMany(ReportStatusLog::class, 'changed_by');
    }
}
