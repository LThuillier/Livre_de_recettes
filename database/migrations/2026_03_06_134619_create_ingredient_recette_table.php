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
       
        Schema::create('ingredient_recette', function (Blueprint $table) {
            // Clés étrangères vers les deux tables
            $table->foreignId('ingredient_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('recette_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Données du pivot
            $table->decimal('quantite', 8, 2);
            $table->string('unite', 50);

            // Clé primaire composite
            $table->primary(['ingredient_id', 'recette_id']);
             });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_recette');
    }
};
