<?php

namespace Database\Seeders;

use App\Models\Blindbox;
use App\Models\Role;
use App\Models\Serie;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $userRole = Role::firstOrCreate(['name' => 'User']);

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'role_id' => $userRole->id,
            ]
        );

        $series = [
            'Skullpanda',
            'Dimoo',
            'Molly',
        ];

        foreach ($series as $serieName) {
            $serie = Serie::firstOrCreate(['name' => $serieName]);

            Blindbox::firstOrCreate(
                ['name' => $serieName . ' Classic'],
                [
                    'serie_id' => $serie->id,
                    'summary' => 'A collectible blindbox from the ' . $serieName . ' collection.',
                    'public' => true,
                    'created_by' => User::where('email', 'test@example.com')->value('id'),
                ]
            );
        }
    }
}
