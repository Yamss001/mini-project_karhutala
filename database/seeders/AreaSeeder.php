<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Kalimantan Barat',
            'Kalimantan Selatan',
            'Kalimantan Tengah',
            'Kalimantan Timur',
            'Jambi',
            'Riau',
            'Sumatera Selatan',
            'Sulawesi Tengah',
            'Sulawesi Tenggara',
            'Sulawesi Selatan',
            'Maluku',
            'Maluku Utara',
            'Nusa Tenggara Timur',
            'Gorontalo',
            'Kalimantan Selatan',
            'Jawa Timur',
        ];

        Area::query()
            ->whereNotIn('name', $areas)
            ->whereDoesntHave('reports')
            ->delete();

        foreach ($areas as $area) {
            Area::firstOrCreate([
                'name' => $area,
            ]);
        }
    }
}
