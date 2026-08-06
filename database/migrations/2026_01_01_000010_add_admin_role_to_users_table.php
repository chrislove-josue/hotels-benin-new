<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL ne permet pas de modifier directement une colonne ENUM via changement de type.
        // On recrée la colonne avec l'ajout de la valeur 'admin'.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // SQLite : pas de ENUM natif, la colonne est un varchar/texte. Rien à faire.
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('voyageur','hote','admin') NOT NULL DEFAULT 'voyageur'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('voyageur','hote') NOT NULL DEFAULT 'voyageur'");
    }
};
