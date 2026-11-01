<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Demo Pelapor',
                'email' => 'pelapor.demo@karhutala.test',
                'password' => 'PelaporDemo#2026',
                'role' => 'pelapor',
            ],
            [
                'name' => 'Demo Relawan',
                'email' => 'relawan.demo@karhutala.test',
                'password' => 'RelawanDemo#2026',
                'role' => 'relawan_pemantau',
            ],
            [
                'name' => 'Demo Koordinator',
                'email' => 'koordinator.demo@karhutala.test',
                'password' => 'KoordinatorDemo#2026',
                'role' => 'koordinator_relawan',
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                ]
            );
        }
    }
}
