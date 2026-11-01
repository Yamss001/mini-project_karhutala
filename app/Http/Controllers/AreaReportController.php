<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Report;
use Illuminate\Http\JsonResponse;

class AreaReportController extends Controller
{
    public function activeReports(Area $area): JsonResponse
    {
        $activeStatuses = [
            'baru',
            'ditinjau',
            'diteruskan',
        ];

        $rows = Report::query()
            ->where('area_id', $area->id)
            ->whereIn('status', $activeStatuses)
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $statusCounts = array_fill_keys(
            $activeStatuses,
            0
        );

        foreach ($rows as $row) {
            $statusCounts[$row->status] = (int) $row->total;
        }

        return response()->json([
            'area' => [
                'id' => $area->id,
                'name' => $area->name,
            ],

            'total_active' => array_sum($statusCounts),

            'status_counts' => $statusCounts,
        ]);
    }
}
