<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class ReportRecapController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'area_id' => [
                'nullable',
                'integer',
                'exists:areas,id',
            ],
        ]);

        $areaId = $filters['area_id'] ?? null;

        $baseQuery = Report::query()
            ->when($areaId, function ($query, $areaId) {
                $query->where('area_id', $areaId);
            });

        $summary = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total_reports')
            ->selectRaw(
                "COALESCE(SUM(
                    CASE WHEN status IN ('baru', 'ditinjau', 'diteruskan')
                    THEN 1 ELSE 0 END
                ), 0) as active_reports"
            )
            ->selectRaw(
                "COALESCE(SUM(
                    CASE WHEN status = 'selesai'
                    THEN 1 ELSE 0 END
                ), 0) as completed_reports"
            )
            ->first();

        $recaps = Area::query()
            ->withCount([
                'reports as baru_count' => function (Builder $query) {
                    $query->where('status', 'baru');
                },

                'reports as ditinjau_count' => function (Builder $query) {
                    $query->where('status', 'ditinjau');
                },

                'reports as diteruskan_count' => function (Builder $query) {
                    $query->where('status', 'diteruskan');
                },

                'reports as selesai_count' => function (Builder $query) {
                    $query->where('status', 'selesai');
                },

                'reports as total_count',
            ])
            ->when($areaId, function ($query, $areaId) {
                $query->where('id', $areaId);
            })
            ->orderBy('name')
            ->get();

        $areas = Area::orderBy('name')->get();

        return view('recap.index', compact(
            'summary',
            'recaps',
            'areas',
            'areaId'
        ));
    }
}
