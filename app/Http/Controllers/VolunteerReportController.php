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

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Report::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'status' => ['nullable', 'in:baru,ditinjau'],
        ]);

        $query = Report::with(['area', 'reporter'])
            ->whereIn('status', ['baru', 'ditinjau']);

        $query->when($filters['search'] ?? null, function ($query, $keyword) {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery
                    ->where('condition', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('reporter', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%");
                    });

                if (ctype_digit($keyword)) {
                    $subQuery->orWhere('id', (int) $keyword);
                }
            });
        });

        $query->when($filters['area_id'] ?? null, function ($query, $areaId) {
            $query->where('area_id', $areaId);
        });

        $query->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });

        $reports = $query
            ->latest()
            ->paginate(5)
            ->withQueryString();

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
