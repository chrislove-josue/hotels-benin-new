<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // voyageur
            $table->foreignId('establishment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete(); // chambre / espace
            $table->date('check_in')->nullable();   // date arrivée (hôtel) / début événement
            $table->date('check_out')->nullable();  // date départ / fin événement
            $table->unsignedInteger('guests')->default(1); // nombre de personnes
            $table->decimal('amount', 10, 2)->default(0);   // montant total
            $table->decimal('commission', 10, 2)->default(0);
            $table->string('payment_method')->nullable();   // momo / card / etc.
            $table->enum('status', ['en_attente', 'confirmee', 'annulee', 'terminee'])->default('en_attente');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('establishment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
