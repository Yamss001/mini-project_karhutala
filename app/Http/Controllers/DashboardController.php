<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $areas = Area::orderBy('name')->get();

        $showRecap = in_array($user->role, [
            'relawan_pemantau',
            'koordinator_relawan',
        ], true);

        $initialRecap = [
            'summary' => [
                'total_reports' => 0,
                'active_reports' => 0,
                'completed_reports' => 0,
            ],
            'rows' => [],
        ];

        if ($showRecap) {

            $summary = Report::query()
                ->selectRaw('COUNT(*) as total_reports')
                ->selectRaw("
                    COALESCE(SUM(
                        CASE
                            WHEN status IN ('baru', 'ditinjau', 'diteruskan')
                            THEN 1 ELSE 0
                        END
                    ), 0) as active_reports
                ")
                ->selectRaw("
                    COALESCE(SUM(
                        CASE
                            WHEN status = 'selesai'
                            THEN 1 ELSE 0
                        END
                    ), 0) as completed_reports
                ")
                ->first();

            $recaps = Area::query()
                ->withCount([
                    'reports as baru_count' => function ($query) {
                        $query->where('status', 'baru');
                    },
                    'reports as ditinjau_count' => function ($query) {
                        $query->where('status', 'ditinjau');
                    },
                    'reports as diteruskan_count' => function ($query) {
                        $query->where('status', 'diteruskan');
                    },
                    'reports as selesai_count' => function ($query) {
                        $query->where('status', 'selesai');
                    },
                    'reports as total_count',
                ])
                ->orderBy('name')
                ->get();

            $initialRecap = [
                'summary' => [
                    'total_reports' => (int) $summary->total_reports,
                    'active_reports' => (int) $summary->active_reports,
                    'completed_reports' => (int) $summary->completed_reports,
                ],

                'rows' => $recaps->map(function ($area) {
                    return [
                        'name' => $area->name,
                        'baru' => (int) $area->baru_count,
                        'ditinjau' => (int) $area->ditinjau_count,
                        'diteruskan' => (int) $area->diteruskan_count,
                        'selesai' => (int) $area->selesai_count,
                        'total' => (int) $area->total_count,
                    ];
                })->values()->all(),
            ];
        }

        return view('dashboard', compact(
            'areas',
            'showRecap',
            'initialRecap'
        ));
    }
}
