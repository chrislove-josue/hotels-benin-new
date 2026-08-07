<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Permet à un utilisateur de poster plusieurs avis sur la même chambre.
        Schema::table('reviews', function (Blueprint $table) {
            // MySQL : l'index unique composite (user_id, room_id) sert d'index
            // à la clé étrangère sur user_id. Il faut donc :
            // 1. retirer la clé étrangère,
            // 2. supprimer l'index unique,
            // 3. recréer la clé étrangère (qui regénère son propre index).
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'room_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unique(['user_id', 'room_id']);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};

