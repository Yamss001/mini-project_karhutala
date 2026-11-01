<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class AreaReportController extends Controller
{
    
    public function activeReports(Area $area): JsonResponse
    {
        $statuses = [
            'baru',
            'ditinjau',
            'diteruskan',
            'selesai',
        ];

        $rows = Report::query()
            ->where('area_id', $area->id)
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $statusCounts = array_fill_keys($statuses, 0);

        foreach ($rows as $row) {
            $statusCounts[$row->status] = (int) $row->total;
        }

        $totalReports = array_sum($statusCounts);

        $totalActive =
            $statusCounts['baru']
            + $statusCounts['ditinjau']
            + $statusCounts['diteruskan'];

        return response()->json([
            'area' => [
                'id' => $area->id,
                'name' => $area->name,
            ],

            'summary' => [
                'total_reports' => $totalReports,
                'active_reports' => $totalActive,
                'completed_reports' => $statusCounts['selesai'],
            ],

            'status_counts' => $statusCounts,

            'total_active' => $totalActive,
        ]);
    }

}
