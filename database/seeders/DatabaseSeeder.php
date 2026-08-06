<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocalitySeeder::class,
            EstablishmentTypeSeeder::class,
            AmenitySeeder::class,
            UserSeeder::class,
            EstablishmentSeeder::class,
            RoomSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
