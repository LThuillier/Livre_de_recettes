<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('recettes', function (Blueprint $table) {
            // Ajouter user_id si elle n'existe pas
            if (!Schema::hasColumn('recettes', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->constrained()
                      ->onDelete('cascade');
            }

            // Ajouter est_public si elle n'existe pas
            if (!Schema::hasColumn('recettes', 'est_public')) {
                $table->boolean('est_public')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('recettes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'est_public']);
        });
    }
};
