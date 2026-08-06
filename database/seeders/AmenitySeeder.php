<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            ['name' => 'Wifi gratuit', 'icon' => 'wifi'],
            ['name' => 'Piscine', 'icon' => 'water'],
            ['name' => 'Climatisation', 'icon' => 'snowflake'],
            ['name' => 'Parking privé', 'icon' => 'truck'],
            ['name' => 'Petit-déjeuner inclus', 'icon' => 'cake'],
            ['name' => 'Groupe électrogène', 'icon' => 'bolt'],
            ['name' => 'Restaurant', 'icon' => 'cake'],
            ['name' => 'Salle de sport', 'icon' => 'fire'],
            ['name' => 'Vue sur mer', 'icon' => 'eye'],
            ['name' => 'Service de blanchisserie', 'icon' => 'sparkles'],
            ['name' => 'Espace évènementiel', 'icon' => 'users'],
            ['name' => 'Sécurité 24h/24', 'icon' => 'shield-check'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }
    }
}
