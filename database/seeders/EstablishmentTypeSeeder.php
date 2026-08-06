<?php

namespace Database\Seeders;

use App\Models\EstablishmentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EstablishmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Hôtel', 'icon' => 'building-office-2'],
            ['name' => 'Résidence meublée', 'icon' => 'home-modern'],
            ['name' => 'Salle de fête', 'icon' => 'sparkles'],
            ['name' => 'Auberge', 'icon' => 'home'],
            ['name' => 'Villa', 'icon' => 'sun'],
        ];

        foreach ($types as $type) {
            EstablishmentType::create([
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'icon' => $type['icon'],
            ]);
        }
    }
}
