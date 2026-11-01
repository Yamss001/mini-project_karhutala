<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VolunteerReportController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Report::class);

        $reports = Report::with(['area', 'reporter'])
            ->whereIn('status', ['baru', 'ditinjau'])
            ->latest()
            ->paginate(10);

        return view('volunteer.reports.index', compact('reports'));
    }

    public function show(Report $report): View
    {
        Gate::authorize('view', $report);

        $report->load([
            'area',
            'reporter',
            'reviews.reviewer',
        ]);

        $relatedReports = Report::with('area')
            ->where('area_id', $report->area_id)
            ->where('id', '!=', $report->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('volunteer.reports.show', compact(
            'report',
            'relatedReports'
        ));
    }

    public function review(
        Request $request,
        Report $report
    ): RedirectResponse {

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use (
            $request,
            $report,
            $validated
        ) {
            $lockedReport = Report::query()
                ->lockForUpdate()
                ->findOrFail($report->id);

            Gate::authorize('review', $lockedReport);

            $review = new ReportReview();
            $review->reviewer_id = $request->user()->id;
            $review->notes = $validated['notes'];
            $review->reviewed_at = now();

            $lockedReport->reviews()->save($review);

            if ($lockedReport->status === 'baru') {
                $lockedReport->status = 'ditinjau';
                $lockedReport->save();
            }
        });

        return redirect()
            ->route('relawan.reports.show', $report)
            ->with('success', 'Catatan peninjauan berhasil disimpan.');
    }

    public function complete(
        Request $request,
        Report $report
    ): RedirectResponse {

        DB::transaction(function () use ($report) {
            $lockedReport = Report::query()
                ->lockForUpdate()
                ->findOrFail($report->id);

            Gate::authorize(
                'completeWithoutForward',
                $lockedReport
            );

            $lockedReport->status = 'selesai';
            $lockedReport->save();
        });

        return redirect()
            ->route('relawan.reports.show', $report)
            ->with('success', 'Laporan berhasil diselesaikan.');
    }
}
