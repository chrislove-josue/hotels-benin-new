<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('localities', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // ex: Cotonou, Porto-Novo, Ouidah...
            $table->string('slug')->unique();
            $table->string('department')->nullable(); // ex: Littoral, Atlantique
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localities');
    }
};
