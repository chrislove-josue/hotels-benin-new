<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // hôte propriétaire
            $table->foreignId('locality_id')->constrained()->cascadeOnDelete();
            $table->foreignId('establishment_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('address')->nullable();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->decimal('price_from', 10, 2)->default(0); // prix d'appel affiché sur les cartes
            $table->unsignedInteger('capacity')->default(1);  // capacité max (personnes) pour salles/résidences
            $table->string('cover_image')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['locality_id', 'establishment_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
