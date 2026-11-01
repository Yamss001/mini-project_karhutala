<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportForwarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CoordinatorReportController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Report::class);

        $reports = Report::with(['area', 'reporter'])
            ->latest()
            ->paginate(10);

        return view('coordinator.reports.index', compact('reports'));
    }

    // Detail laporan
    public function show(Report $report): View
    {
        Gate::authorize('view', $report);

        $report->load([
            'area',
            'reporter',
            'reviews.reviewer',
            'forwardings.coordinator',
        ]);

        return view('coordinator.reports.show', compact('report'));
    }

    public function forward(
        Request $request,
        Report $report
    ): RedirectResponse {

        $validated = $request->validate([
            'recipient_name' => [
                'required',
                'string',
                'max:150',
            ],
            'forwarded_at' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $report,
            $validated
        ) {
            $lockedReport = Report::query()
                ->lockForUpdate()
                ->findOrFail($report->id);

            Gate::authorize('forward', $lockedReport);

            $forwarding = new ReportForwarding();

            $forwarding->coordinator_id = $request->user()->id;
            $forwarding->recipient_name = $validated['recipient_name'];
            $forwarding->forwarded_at = $validated['forwarded_at'];
            $forwarding->notes = $validated['notes'] ?? null;

            $lockedReport->forwardings()->save($forwarding);

            // Status berubah setelah penerusan tercatat
            if ($lockedReport->status === 'ditinjau') {
                $lockedReport->status = 'diteruskan';
                $lockedReport->save();
            }
        });

        return redirect()
            ->route('koordinator.reports.show', $report)
            ->with('success', 'Penerusan informasi berhasil dicatat.');
    }

    public function complete(Report $report): RedirectResponse
    {
        DB::transaction(function () use ($report) {
            $lockedReport = Report::query()
                ->lockForUpdate()
                ->findOrFail($report->id);

            Gate::authorize(
                'completeForwarded',
                $lockedReport
            );

            $lockedReport->status = 'selesai';
            $lockedReport->save();
        });

        return redirect()
            ->route('koordinator.reports.show', $report)
            ->with('success', 'Proses laporan berhasil diselesaikan.');
    }
}
