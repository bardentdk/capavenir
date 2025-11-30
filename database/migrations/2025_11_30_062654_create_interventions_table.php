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
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');

            $table->dateTime('start_at');
            $table->dateTime('end_at');

            // IMPORTANT : Doit être un integer simple, PAS un virtualAs
            $table->integer('duration_minutes')->nullable();

            $table->string('location_type');
            $table->string('intervention_type');

            $table->text('raw_notes')->nullable();
            $table->longText('ai_report')->nullable(); // On s'assure que c'est bien longText

            $table->string('status')->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
