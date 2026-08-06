<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // ex: Chambre Standard, Suite Junior, Salle A
            $table->text('description')->nullable();
            $table->unsignedInteger('capacity')->default(2);
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedInteger('quantity')->default(1); // nombre d'unités identiques
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
