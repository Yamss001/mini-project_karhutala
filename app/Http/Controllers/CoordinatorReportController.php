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

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Report::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
            'status' => ['nullable', 'in:baru,ditinjau,diteruskan,selesai'],
        ]);

        $query = Report::with(['area', 'reporter']);

        $query->when($filters['search'] ?? null, function ($query, $keyword) {
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery
                    ->where('condition', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhereHas('reporter', function ($userQuery) use ($keyword) {
                        $userQuery->where('name', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('area', function ($areaQuery) use ($keyword) {
                        $areaQuery->where('name', 'like', "%{$keyword}%");
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

        return view('coordinator.reports.index', compact('reports'));
    }

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
