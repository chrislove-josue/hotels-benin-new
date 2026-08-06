<?php

namespace Database\Seeders;

use App\Models\Establishment;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    private array $titles = [
        'Excellent séjour', 'Très bon accueil', 'Rapport qualité-prix correct',
        'Nous reviendrons !', 'Un peu bruyant mais agréable', 'Cadre magnifique',
        'Personnel très attentionné', 'Conforme aux photos', 'Belle découverte',
    ];

    private array $comments = [
        "Séjour très agréable, personnel accueillant et chambres propres. Je recommande vivement.",
        "L'emplacement est idéal, proche de tout. Petit-déjeuner copieux et varié.",
        "Bon rapport qualité-prix pour la région. Quelques détails à améliorer côté climatisation.",
        "Cadre magnifique et calme, parfait pour se reposer en famille.",
        "Accueil chaleureux dès l'arrivée, on se sent vraiment bien reçu.",
        "Établissement propre et bien situé, nous avons passé un excellent moment.",
        "Un peu de bruit le soir mais l'ensemble reste très satisfaisant.",
        "Le service en salle était impeccable et le personnel très disponible.",
        "Conforme à la description, aucune mauvaise surprise, à recommander.",
        "Nous avons adoré la piscine et le jardin, un vrai coin de paradis.",
    ];

    public function run(): void
    {
        $voyageurs = User::where('role', 'voyageur')->get();

        Establishment::all()->each(function (Establishment $establishment) use ($voyageurs) {
            $reviewers = $voyageurs->random(min(random_int(3, 7), $voyageurs->count()));
            $ratings = [];

            foreach ($reviewers as $user) {
                $rating = random_int(3, 5);
                $ratings[] = $rating;

                Review::create([
                    'establishment_id' => $establishment->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'title' => $this->titles[array_rand($this->titles)],
                    'comment' => $this->comments[array_rand($this->comments)],
                ]);
            }

            if (count($ratings) > 0) {
                $establishment->update([
                    'rating' => round(array_sum($ratings) / count($ratings), 2),
                    'reviews_count' => count($ratings),
                ]);
            }
        });
    }
}
