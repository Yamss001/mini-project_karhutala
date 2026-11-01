<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'pelapor',
            'relawan_pemantau',
            'koordinator_relawan',
        ], true);
    }

    public function view(User $user, Report $report): bool
    {
        return in_array($user->role, [
            'relawan_pemantau',
            'koordinator_relawan',
        ], true)
        || (
            $user->role === 'pelapor'
            && $report->reporter_id === $user->id
        );
    }

    public function create(User $user): bool
    {
        return $user->role === 'pelapor';
    }

    public function update(User $user, Report $report): bool
    {
        return $user->role === 'pelapor'
            && $report->reporter_id === $user->id
            && $report->status === 'baru';
    }

    public function review(User $user, Report $report): bool
    {
        return $user->role === 'relawan_pemantau'
            && in_array($report->status, ['baru', 'ditinjau'], true);
    }

    public function completeWithoutForward(
        User $user,
        Report $report
    ): bool {
        return $user->role === 'relawan_pemantau'
            && $report->status === 'ditinjau'
            && !$report->forwardings()->exists();
    }

    public function forward(User $user, Report $report): bool
    {
        return $user->role === 'koordinator_relawan'
            && in_array($report->status, ['ditinjau', 'diteruskan'], true);
    }

    public function completeForwarded(
        User $user,
        Report $report
    ): bool {
        return $user->role === 'koordinator_relawan'
            && $report->status === 'diteruskan'
            && $report->forwardings()->exists();
    }

    public function delete(User $user, Report $report): bool
    {
        return false;
    }

    public function restore(User $user, Report $report): bool
    {
        return false;
    }

    public function forceDelete(User $user, Report $report): bool
    {
        return false;
    }
}
