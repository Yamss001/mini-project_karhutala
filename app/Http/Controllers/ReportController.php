<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ReportController extends Controller
{
    // Menampilkan daftar laporan milik Pelapor
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Report::class);

        $reports = $request->user()
            ->reports()
            ->with('area')
            ->latest()
            ->paginate(5);

        return view('reports.index', compact('reports'));
    }

    // Menampilkan form laporan
    public function create(): View
    {
        Gate::authorize('create', Report::class);

        $areas = Area::orderBy('name')->get();
        $report = new Report();

        return view('reports.create', compact('areas', 'report'));
    }

    // Menyimpan laporan baru
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Report::class);

        $validated = $request->validate($this->reportRules());

        $evidence = $validated['evidence'] ?? null;
        unset($validated['evidence']);

        $evidencePath = null;

        if ($evidence !== null) {
            $evidencePath = $evidence->store(
                'reports/evidence',
                'local'
            );

            if ($evidencePath === false) {
                return back()
                    ->withErrors(['evidence' => 'Gagal menyimpan evidence.'])
                    ->withInput();
            }
        }

        try {
            $report = DB::transaction(function () use (
                $request,
                $validated,
                $evidencePath
            ) {
                $report = new Report($validated);

                $report->reporter()->associate($request->user());

                $report->status = 'baru';

                $report->evidence_path = $evidencePath;

                $report->save();

                return $report;
            });
        } catch (Throwable $exception) {
            if ($evidencePath !== null) {
                Storage::disk('local')->delete($evidencePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Laporan berhasil dibuat.');
    }

    public function show(Report $report): View
    {
        Gate::authorize('view', $report);

        $report->load([
            'reporter',
            'area',
            'statusLogs.changedBy',
        ]);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report): View
    {
        Gate::authorize('update', $report);

        $areas = Area::orderBy('name')->get();

        return view('reports.edit', compact('report', 'areas'));
    }

    public function update(
        Request $request,
        Report $report
    ): RedirectResponse {

        Gate::authorize('update', $report);

        $validated = $request->validate($this->reportRules());

        $evidence = $validated['evidence'] ?? null;
        unset($validated['evidence']);

        $oldEvidencePath = $report->evidence_path;
        $newEvidencePath = null;

        if ($evidence !== null) {
            $newEvidencePath = $evidence->store(
                'reports/evidence',
                'local'
            );

            if ($newEvidencePath === false) {
                return back()
                    ->withErrors(['evidence' => 'Gagal menyimpan evidence.'])
                    ->withInput();
            }
        }

        try {
            DB::transaction(function () use (
                $report,
                $validated,
                $newEvidencePath
            ) {
                $report->fill($validated);

                if ($newEvidencePath !== null) {
                    $report->evidence_path = $newEvidencePath;
                }

                $report->save();
            });
        } catch (Throwable $exception) {
            if ($newEvidencePath !== null) {
                Storage::disk('local')->delete($newEvidencePath);
            }

            throw $exception;
        }

        // Hapus evidence lama setelah perubahan database berhasil
        if ($newEvidencePath !== null && $oldEvidencePath !== null) {
            Storage::disk('local')->delete($oldEvidencePath);
        }

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    public function downloadEvidence(
        Report $report
    ): StreamedResponse {

        Gate::authorize('view', $report);

        abort_unless(
            $report->evidence_path &&
            Storage::disk('local')->exists($report->evidence_path),
            404
        );

        return Storage::disk('local')->download(
            $report->evidence_path
        );
    }

    private function reportRules(): array
    {
        return [
            'area_id' => [
                'required',
                'integer',
                'exists:areas,id',
            ],

            'incident_at' => [
                'required',
                'date',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'condition' => [
                'required',
                'string',
                'max:150',
            ],

            'description' => [
                'required',
                'string',
                'max:5000',
            ],

            'evidence' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ],
        ];
    }
}
