<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder complémentaire ajoutant de nouveaux événements béninois.
 * Utilise updateOrCreate (clé : title) pour être relançable sans doublons.
 */
class ExtraEventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title' => 'Festival des masques de Porto-Novo',
                'category' => 'culture',
                'short_description' => 'Une célébration colorée des masques traditionnels du pays Yoruba au cœur de la capitale.',
                'description' => 'Porto-Novo, capitale politique du Bénin, célèbre chaque année la richesse de la culture des masques Yoruba. Danses masquées, sorties des egungun, chants et rituels se succèdent dans les rues de la ville. Un événement haut en couleurs pour découvrir le patrimoine immatériel du Bénin.',
                'date_start' => now()->addMonths(1)->startOfMonth()->addDays(5)->toDateString(),
                'date_end' => now()->addMonths(1)->startOfMonth()->addDays(7)->toDateString(),
                'location' => 'Porto-Novo',
                'cover_image' => 'https://images.unsplash.com/photo-1520769669658-f07657f5a307?w=1200&q=80',
                'is_featured' => true,
            ],
            [
                'title' => 'Foire Internationale de Parakou (FIPA)',
                'category' => 'affaires',
                'short_description' => 'Le grand rendez-vous économique et commercial du septentrion béninois.',
                'description' => 'La Foire Internationale de Parakou rassemble exposants, commerçants et industriels du Bénin et des pays voisins. Produits agricoles, artisanat, équipements : une vitrine complète du dynamisme économique du nord du pays, accompagnée de conférences et d\'animations.',
                'date_start' => now()->addMonths(3)->startOfMonth()->addDays(10)->toDateString(),
                'date_end' => now()->addMonths(3)->startOfMonth()->addDays(17)->toDateString(),
                'location' => 'Parakou',
                'cover_image' => 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=1200&q=80',
                'is_featured' => true,
            ],
            [
                'title' => 'WeLoveEya — Évasion marine et culturelle',
                'category' => 'festival',
                'short_description' => 'Un festival festif au bord de l\'océan à Cotonou, mélange de musique, plage et culture.',
                'description' => 'WeLoveEya transforme les plages de Cotonou en un immense espace de fête et de découverte. Concerts, activités nautiques, expositions d\'art, gastronomie locale et animations pour toute la famille. Une célébration unique du patrimoine maritime et culturel du Bénin.',
                'date_start' => now()->addMonths(5)->startOfMonth()->addDays(15)->toDateString(),
                'date_end' => now()->addMonths(5)->startOfMonth()->addDays(18)->toDateString(),
                'location' => 'Cotonou',
                'cover_image' => 'https://images.unsplash.com/photo-1519046904884-53103b34b206?w=1200&q=80',
                'is_featured' => true,
            ],
            [
                'title' => 'Grand concert à l\'Esplanade de l\'Amazone',
                'category' => 'festival',
                'short_description' => 'Des concerts géants sur la mythique Esplanade de l\'Amazone, nouvelle place culturelle de Cotonou.',
                'description' => 'L\'Esplanade de l\'Amazone, la nouvelle place emblématique de Cotonou, accueille des concerts et spectacles de grande envergure. Artistes béninois et internationaux s\'y produisent dans une ambiance électrique au cœur de la capitale économique. Un lieu à ne pas manquer.',
                'date_start' => now()->addMonths(2)->startOfMonth()->addDays(3)->toDateString(),
                'date_end' => now()->addMonths(2)->startOfMonth()->addDays(3)->toDateString(),
                'location' => 'Cotonou (Esplanade de l\'Amazone)',
                'cover_image' => 'https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?w=1200&q=80',
                'is_featured' => false,
            ],
            [
                'title' => 'Fête de l\'igname de Savalou',
                'category' => 'culture',
                'short_description' => 'La célèbre fête traditionnelle de l\'igname dans le département des Collines.',
                'description' => 'La Fête de l\'igname de Savalou est une des grandes célébrations traditionnelles du Bénin. Elle marque la fin des récoltes et rend hommage aux ancêtres. Défilés de chefs, danses traditionnelles, dégustations de mets à base d\'igname et cérémonies rituelles animent la ville pendant plusieurs jours.',
                'date_start' => now()->addMonths(6)->startOfMonth()->addDays(10)->toDateString(),
                'date_end' => now()->addMonths(6)->startOfMonth()->addDays(12)->toDateString(),
                'location' => 'Savalou',
                'cover_image' => 'https://images.unsplash.com/photo-1512850183-6d7990f42385?w=1200&q=80',
                'is_featured' => false,
            ],
            [
                'title' => 'Fête de la Gani de Kandi',
                'category' => 'culture',
                'short_description' => 'La fin de la saison des récoltes célébrée avec courses de chevaux et traditions Bariba.',
                'description' => 'La Gani de Kandi est une fête traditionnelle du peuple Bariba marquant la fin de la saison des récoltes. Au programme : défilés royaux, courses de chevaux, danses et cérémonies dans une ambiance conviviale au nord du Bénin.',
                'date_start' => now()->addMonths(4)->startOfMonth()->addDays(20)->toDateString(),
                'date_end' => now()->addMonths(4)->startOfMonth()->addDays(22)->toDateString(),
                'location' => 'Kandi',
                'cover_image' => 'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=1200&q=80',
                'is_featured' => false,
            ],
            [
                'title' => 'Journée des Amazones du Danxomè — Abomey',
                'category' => 'culture',
                'short_description' => 'Hommage aux célèbres guerrières Amazones du royaume du Dahomey.',
                'description' => 'La Journée des Amazones du Danxomè rend hommage aux légendaires guerrières du royaume du Dahomey, inscrites au patrimoine mondial de l\'UNESCO. Représentations historiques, défilés et danses traditionnelles font revivre cette page glorieuse de l\'histoire du Bénin à Abomey.',
                'date_start' => now()->addMonths(3)->startOfMonth()->addDays(1)->toDateString(),
                'date_end' => now()->addMonths(3)->startOfMonth()->addDays(2)->toDateString(),
                'location' => 'Abomey',
                'cover_image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?w=1200&q=80',
                'is_featured' => false,
            ],
            [
                'title' => 'Festival International du Rire de Cotonou',
                'category' => 'culture',
                'short_description' => 'Le rendez-vous de l\'humour et de la comédie africaine dans la capitale économique.',
                'description' => 'Le Festival International du Rire de Cotonou réunit les plus grands humoristes du continent pour des soirées de stand-up, sketches et spectacles. Une semaine de rires et de bonne humeur qui attire un large public et les amateurs de comédie africaine.',
                'date_start' => now()->addMonths(4)->startOfMonth()->addDays(8)->toDateString(),
                'date_end' => now()->addMonths(4)->startOfMonth()->addDays(11)->toDateString(),
                'location' => 'Cotonou',
                'cover_image' => 'https://images.unsplash.com/photo-1527224857830-43a7acc85260?w=1200&q=80',
                'is_featured' => false,
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['title' => $event['title']],
                [
                    'slug' => Str::slug($event['title']),
                    'category' => $event['category'],
                    'short_description' => $event['short_description'],
                    'description' => $event['description'],
                    'date_start' => $event['date_start'],
                    'date_end' => $event['date_end'],
                    'location' => $event['location'],
                    'cover_image' => $event['cover_image'],
                    'is_featured' => $event['is_featured'],
                    'is_available' => true,
                ]
            );
        }
    }
}
