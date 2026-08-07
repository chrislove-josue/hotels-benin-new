<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('category', ['festival', 'culture', 'sport', 'affaires', 'religieux'])->default('culture');
            $table->text('short_description')->nullable();
            $table->text('description');
            $table->date('date_start');
            $table->date('date_end')->nullable();
            $table->string('location')->nullable();
            $table->string('cover_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index(['category', 'date_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

