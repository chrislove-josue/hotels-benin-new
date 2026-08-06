<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $roomTemplates = [
            ['name' => 'Chambre Standard', 'capacity' => 2, 'factor' => 1.0],
            ['name' => 'Chambre Confort', 'capacity' => 2, 'factor' => 1.3],
            ['name' => 'Suite Junior', 'capacity' => 3, 'factor' => 1.8],
            ['name' => 'Suite Prestige', 'capacity' => 4, 'factor' => 2.4],
        ];

        Establishment::all()->each(function (Establishment $establishment) use ($roomTemplates) {
            // Les salles de fête n'ont pas de "chambres" mais un espace unique
            if ($establishment->type->slug === 'salle-de-fete') {
                Room::create([
                    'establishment_id' => $establishment->id,
                    'name' => 'Location de la salle (journée)',
                    'description' => 'Accès à la salle équipée pour une journée complète, incluant tables, chaises et sonorisation de base.',
                    'capacity' => $establishment->capacity,
                    'price_per_night' => $establishment->price_from,
                    'quantity' => 1,
                    'is_available' => true,
                ]);

                return;
            }

            $count = random_int(2, 4);
            foreach (array_slice($roomTemplates, 0, $count) as $template) {
                Room::create([
                    'establishment_id' => $establishment->id,
                    'name' => $template['name'],
                    'description' => 'Literie confortable, salle de bain privative et climatisation individuelle.',
                    'capacity' => min($template['capacity'], max(1, $establishment->capacity)),
                    'price_per_night' => round($establishment->price_from * $template['factor'], -2),
                    'quantity' => random_int(2, 8),
                    'is_available' => true,
                ]);
            }
        });
    }
}
