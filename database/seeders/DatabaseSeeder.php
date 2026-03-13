<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Compte admin
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@winboard.local',
            'password' => Hash::make('admin1234'),
            'role'     => 'admin',
            'points'   => 0,
        ]);

        // Commerciaux exemples (à personnaliser)
        $commerciaux = ['Alice', 'Bob', 'Charlie', 'David'];
        foreach ($commerciaux as $i => $name) {
            User::create([
                'name'     => $name,
                'email'    => strtolower($name) . '@winboard.local',
                'password' => Hash::make('password'),
                'role'     => 'commercial',
                'points'   => 0,
            ]);
        }

        // Badges de départ
        $badges = [
            [
                'name'            => 'Premier Pas',
                'description'     => 'Décrocher votre premier RDV',
                'icon'            => '🎯',
                'condition_type'  => 'appointments_count',
                'condition_value' => 1,
            ],
            [
                'name'            => 'En Route',
                'description'     => '5 RDV décrochés',
                'icon'            => '🚀',
                'condition_type'  => 'appointments_count',
                'condition_value' => 5,
            ],
            [
                'name'            => 'Chasseur d\'Or',
                'description'     => 'Décrocher un RDV niveau Gold',
                'icon'            => '🥇',
                'condition_type'  => 'gold_count',
                'condition_value' => 1,
            ],
            [
                'name'            => 'Centurion',
                'description'     => 'Atteindre 100 points',
                'icon'            => '💯',
                'condition_type'  => 'points_total',
                'condition_value' => 100,
            ],
            [
                'name'            => 'Machine de Guerre',
                'description'     => '10 RDV décrochés',
                'icon'            => '⚡',
                'condition_type'  => 'appointments_count',
                'condition_value' => 10,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
