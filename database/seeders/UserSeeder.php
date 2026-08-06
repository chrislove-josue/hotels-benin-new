<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Quelques hôtes (gestionnaires d'établissements) nommés, pour des données réalistes
        $hotes = [
            ['name' => 'Fabrice Dossou', 'email' => 'fabrice.dossou@hotels-benin.bj'],
            ['name' => 'Rachelle Agbodjan', 'email' => 'rachelle.agbodjan@hotels-benin.bj'],
            ['name' => 'Ismaël Baba-Moussa', 'email' => 'ismael.baba-moussa@hotels-benin.bj'],
            ['name' => 'Colette Hounkpatin', 'email' => 'colette.hounkpatin@hotels-benin.bj'],
            ['name' => 'Bienvenu Zinsou', 'email' => 'bienvenu.zinsou@hotels-benin.bj'],
        ];

        foreach ($hotes as $hote) {
            User::create([
                'name' => $hote['name'],
                'email' => $hote['email'],
                'password' => Hash::make('password'),
                'role' => 'hote',
                'phone' => '+229 ' . random_int(90000000, 99999999),
            ]);
        }

        // Voyageurs (pour les avis)
        User::factory()->count(25)->create([
            'role' => 'voyageur',
            'password' => Hash::make('password'),
        ]);
    }
}
