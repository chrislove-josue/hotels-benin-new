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
            ['name' => 'Chambre Standard', 'capacity' => 2, 'factor' => 1.0, 'cover' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=800&q=80'],
            ['name' => 'Chambre Confort', 'capacity' => 2, 'factor' => 1.3, 'cover' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=800&q=80'],
            ['name' => 'Suite Junior', 'capacity' => 3, 'factor' => 1.8, 'cover' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&q=80'],
            ['name' => 'Suite Prestige', 'capacity' => 4, 'factor' => 2.4, 'cover' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800&q=80'],
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
                    'cover_image' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=800&q=80',
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
                    'cover_image' => $template['cover'],
                ]);
            }
        });
    }
}
