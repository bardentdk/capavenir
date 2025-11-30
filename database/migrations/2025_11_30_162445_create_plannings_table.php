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
        Schema::create('plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // L'éducateur concerné
            $table->foreignId('creator_id')->constrained('users'); // Qui a créé l'event (Compta)

            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');

            // Type pour la couleur (intervention, réunion, formation...)
            $table->string('type')->default('intervention');
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plannings');
    }
};
