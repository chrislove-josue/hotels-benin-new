<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('establishment_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');   // Hôtel, Résidence meublée, Salle de fête, Auberge...
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('establishment_types');
    }
};
