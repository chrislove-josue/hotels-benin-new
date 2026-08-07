<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Lie un avis à une chambre précise (nullable car certains avis restent au niveau établissement)
            $table->foreignId('room_id')->nullable()->after('establishment_id')->constrained()->cascadeOnDelete();

            // Un utilisateur ne peut donner qu'un seul avis par chambre
            $table->unique(['user_id', 'room_id']);
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'room_id']);
            $table->dropConstrainedForeignId('room_id');
        });
    }
};

