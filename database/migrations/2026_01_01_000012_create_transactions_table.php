<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('commission', 10, 2)->default(0);
            $table->string('method')->nullable();   // MTN MoMo / Moov Money / carte
            $table->string('reference')->nullable(); // référence externe du prestataire
            $table->enum('status', ['en_attente', 'reussie', 'echouee', 'remboursee'])->default('en_attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
