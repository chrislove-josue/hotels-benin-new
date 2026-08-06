<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Establishment;
use App\Models\EstablishmentImage;
use App\Models\EstablishmentType;
use App\Models\Locality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EstablishmentSeeder extends Seeder
{
    public function run(): void
    {
        $hotes = User::where('role', 'hote')->pluck('id')->all();
        $amenityIds = Amenity::pluck('id')->all();

        $establishments = [
            [
                'name' => 'Hôtel du Lac Cotonou',
                'type' => 'Hôtel',
                'locality' => 'Cotonou',
                'short' => 'Hôtel 4 étoiles en bord de lagune, à deux pas du centre d\'affaires.',
                'description' => "L'Hôtel du Lac Cotonou domine la lagune et offre un cadre élégant pour les séjours d'affaires comme de loisirs. Chambres climatisées, restaurant gastronomique et piscine à débordement font de cet établissement une référence dans la capitale économique.",
                'price_from' => 65000,
                'capacity' => 2,
                'featured' => true,
                'rating_seed' => 4.6,
                'img' => 'photo-1566073771259-6a8506099945',
            ],
            [
                'name' => 'Résidence Golden Palm',
                'type' => 'Résidence meublée',
                'locality' => 'Cotonou',
                'short' => 'Appartements meublés haut de gamme au cœur des Cocotiers.',
                'description' => "La Résidence Golden Palm propose des appartements entièrement meublés et équipés, idéals pour les séjours de moyenne et longue durée. Cuisine équipée, wifi fibre et sécurité 24h/24 dans un quartier calme et résidentiel.",
                'price_from' => 45000,
                'capacity' => 4,
                'featured' => true,
                'rating_seed' => 4.4,
                'img' => 'photo-1502672260266-1c1ef2d93688',
            ],
            [
                'name' => 'Villa Sunset Grand-Popo',
                'type' => 'Villa',
                'locality' => 'Grand-Popo',
                'short' => 'Villa face à l\'océan, cadre paisible pour se ressourcer.',
                'description' => "Nichée entre cocotiers et plage de sable fin, la Villa Sunset propose un cadre unique pour un séjour détente en famille ou entre amis. Terrasse privée, hamacs et accès direct à la plage.",
                'price_from' => 55000,
                'capacity' => 8,
                'featured' => true,
                'rating_seed' => 4.8,
                'img' => 'photo-1499793983690-e29da59ef1c2',
            ],
            [
                'name' => 'Auberge du Musée Ouidah',
                'type' => 'Auberge',
                'locality' => 'Ouidah',
                'short' => 'Auberge chaleureuse proche de la Route des Esclaves.',
                'description' => "Cette auberge familiale propose des chambres simples et confortables à proximité des principaux sites historiques et culturels de Ouidah. Petit-déjeuner traditionnel inclus.",
                'price_from' => 20000,
                'capacity' => 3,
                'featured' => false,
                'rating_seed' => 4.1,
                'img' => 'photo-1521783988139-89397d761dce',
            ],
            [
                'name' => 'Salle des Fêtes Royale Abomey',
                'type' => 'Salle de fête',
                'locality' => 'Abomey',
                'short' => 'Grande salle climatisée pour mariages et cérémonies.',
                'description' => "Idéalement située au cœur de l'ancienne cité royale, cette salle des fêtes peut accueillir jusqu'à 300 invités. Sonorisation, éclairage d'ambiance et parking privé sont inclus.",
                'price_from' => 150000,
                'capacity' => 300,
                'featured' => false,
                'rating_seed' => 4.3,
                'img' => 'photo-1519167758481-83f550bb49b3',
            ],
            [
                'name' => 'Hôtel Bel Azur Porto-Novo',
                'type' => 'Hôtel',
                'locality' => 'Porto-Novo',
                'short' => 'Hôtel de charme proche du Palais Royal.',
                'description' => "L'Hôtel Bel Azur allie architecture afro-brésilienne traditionnelle et confort moderne. Restaurant local, jardin tropical et navette vers le centre-ville.",
                'price_from' => 38000,
                'capacity' => 2,
                'featured' => true,
                'rating_seed' => 4.5,
                'img' => 'photo-1551882547-ff40c63fe5fa',
            ],
            [
                'name' => 'Résidence Baobab Abomey-Calavi',
                'type' => 'Résidence meublée',
                'locality' => 'Abomey-Calavi',
                'short' => 'Studios et appartements modernes près du campus universitaire.',
                'description' => "Idéale pour les séjours professionnels ou étudiants, la Résidence Baobab propose des studios fonctionnels avec kitchenette, wifi et espace de coworking commun.",
                'price_from' => 28000,
                'capacity' => 2,
                'featured' => false,
                'rating_seed' => 4.0,
                'img' => 'photo-1522708323590-d24dbb6b0267',
            ],
            [
                'name' => 'Hôtel Savana Parakou',
                'type' => 'Hôtel',
                'locality' => 'Parakou',
                'short' => 'Étape confortable dans le nord du Bénin.',
                'description' => "L'Hôtel Savana Parakou est un point de chute idéal pour explorer le nord du pays. Chambres spacieuses, restaurant panafricain et grande cour ombragée.",
                'price_from' => 32000,
                'capacity' => 2,
                'featured' => false,
                'rating_seed' => 4.2,
                'img' => 'photo-1571003123894-1f0594d2b5d9',
            ],
            [
                'name' => 'Villa des Tisserands Natitingou',
                'type' => 'Villa',
                'locality' => 'Natitingou',
                'short' => 'Villa nichée au pied des montagnes de l\'Atacora.',
                'description' => "Un havre de calme entouré de collines, parfait comme base pour visiter les tata somba environnants. Décoration inspirée de l'artisanat local.",
                'price_from' => 42000,
                'capacity' => 6,
                'featured' => false,
                'rating_seed' => 4.7,
                'img' => 'photo-1590490360182-c33d57733427',
            ],
            [
                'name' => 'Salle Prestige Bohicon',
                'type' => 'Salle de fête',
                'locality' => 'Bohicon',
                'short' => 'Espace évènementiel moderne, idéal pour séminaires.',
                'description' => "La Salle Prestige Bohicon dispose d'équipements audiovisuels complets et d'une capacité modulable, parfaite pour conférences, séminaires ou réceptions.",
                'price_from' => 90000,
                'capacity' => 200,
                'featured' => false,
                'rating_seed' => 4.0,
                'img' => 'photo-1519167758481-83f550bb49b3',
            ],
            [
                'name' => 'Auberge du Pêcheur Lokossa',
                'type' => 'Auberge',
                'locality' => 'Lokossa',
                'short' => 'Petite auberge conviviale près du fleuve Mono.',
                'description' => "Gérée par une famille locale, cette auberge propose des chambres simples et un accueil chaleureux, avec possibilité d'excursions en pirogue sur le fleuve Mono.",
                'price_from' => 15000,
                'capacity' => 3,
                'featured' => false,
                'rating_seed' => 3.9,
                'img' => 'photo-1445019980597-93fa8acb246c',
            ],
            [
                'name' => 'Hôtel Marina Cotonou',
                'type' => 'Hôtel',
                'locality' => 'Cotonou',
                'short' => 'Vue imprenable sur l\'océan Atlantique.',
                'description' => "Situé sur la corniche de Cotonou, l'Hôtel Marina propose des chambres avec vue sur mer, un spa et un rooftop bar très prisé au coucher du soleil.",
                'price_from' => 72000,
                'capacity' => 2,
                'featured' => true,
                'rating_seed' => 4.9,
                'img' => 'photo-1520250497591-112f2f40a3f4',
            ],
        ];

        foreach ($establishments as $data) {
            $locality = Locality::where('name', $data['locality'])->first();
            $type = EstablishmentType::where('name', $data['type'])->first();

            $establishment = Establishment::create([
                'user_id' => $hotes[array_rand($hotes)],
                'locality_id' => $locality->id,
                'establishment_type_id' => $type->id,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'address' => 'Quartier ' . fake('fr_FR')->citySuffix() . ', ' . $data['locality'],
                'description' => $data['description'],
                'short_description' => $data['short'],
                'price_from' => $data['price_from'],
                'capacity' => $data['capacity'],
                'cover_image' => "https://images.unsplash.com/{$data['img']}?w=1200&q=80",
                'rating' => $data['rating_seed'],
                'reviews_count' => 0,
                'is_featured' => $data['featured'],
                'is_available' => true,
                'latitude' => fake('fr_FR')->latitude(6.2, 11.4),
                'longitude' => fake('fr_FR')->longitude(0.8, 3.8),
            ]);

            // Galerie photo (3 images par établissement, mêmes thèmes Unsplash)
            $gallery = [
                $data['img'],
                'photo-1611892440504-42a792e24d32',
                'photo-1582719478250-c89cae4dc85b',
            ];

            foreach ($gallery as $i => $img) {
                EstablishmentImage::create([
                    'establishment_id' => $establishment->id,
                    'path' => "https://images.unsplash.com/{$img}?w=1000&q=80",
                    'alt' => $establishment->name . ' - photo ' . ($i + 1),
                    'position' => $i,
                ]);
            }

            // Équipements aléatoires (entre 4 et 8)
            $randomAmenities = collect($amenityIds)->shuffle()->take(random_int(4, 8))->all();
            $establishment->amenities()->attach($randomAmenities);
        }
    }
}
