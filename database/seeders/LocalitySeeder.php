<?php

namespace Database\Seeders;

use App\Models\Locality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LocalitySeeder extends Seeder
{
    public function run(): void
    {
        $localities = [
            ['name' => 'Cotonou', 'department' => 'Littoral'],
            ['name' => 'Porto-Novo', 'department' => 'Ouémé'],
            ['name' => 'Ouidah', 'department' => 'Atlantique'],
            ['name' => 'Abomey', 'department' => 'Zou'],
            ['name' => 'Abomey-Calavi', 'department' => 'Atlantique'],
            ['name' => 'Grand-Popo', 'department' => 'Mono'],
            ['name' => 'Parakou', 'department' => 'Borgou'],
            ['name' => 'Natitingou', 'department' => 'Atacora'],
            ['name' => 'Bohicon', 'department' => 'Zou'],
            ['name' => 'Lokossa', 'department' => 'Mono'],
        ];

        foreach ($localities as $locality) {
            Locality::create([
                'name' => $locality['name'],
                'slug' => Str::slug($locality['name']),
                'department' => $locality['department'],
            ]);
        }
    }
}
