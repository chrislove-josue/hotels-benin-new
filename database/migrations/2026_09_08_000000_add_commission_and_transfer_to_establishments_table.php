<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            // Commission plateforme par établissement (entre 20 et 30 %).
            $table->decimal('commission_pct', 5, 2)->default(25.00)->after('price_from');

            // Service automobile d'accueil des voyageurs (transfert aéroport / navette).
            $table->boolean('transfer_available')->default(false)->after('commission_pct');
            $table->decimal('transfer_fee', 10, 2)->nullable()->after('transfer_available');
            $table->string('transfer_description', 255)->nullable()->after('transfer_fee');
        });
    }

    public function down(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->dropColumn(['commission_pct', 'transfer_available', 'transfer_fee', 'transfer_description']);
        });
    }
};