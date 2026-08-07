<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute une colonne cover_image à la table des chambres.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('cover_image')->nullable()->after('price_per_night');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('cover_image');
        });
    }
};
