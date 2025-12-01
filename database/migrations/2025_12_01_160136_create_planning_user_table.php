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
        Schema::create('planning_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Optionnel : On peut supprimer la colonne user_id de la table plannings originale
        // Mais pour la transition, on peut la laisser nullable ou la supprimer plus tard.
        // Pour simplifier cette migration, on va supposer qu'on garde la compatibilité
        // mais qu'on n'utilisera plus la colonne 'user_id' directe.
        Schema::table('plannings', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planning_user');
    }
};
