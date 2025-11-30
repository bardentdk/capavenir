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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('client_id')->nullable()->constrained(); // Optionnel (ex: trajet pur)
            $table->foreignId('intervention_id')->nullable()->constrained(); // Lien direct si possible

            $table->date('expense_date');
            $table->string('type'); // 'mileage', 'food', 'activity', 'material'

            // Pour les indemnités kilométriques
            $table->string('start_address')->nullable();
            $table->string('end_address')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();

            // Montants
            $table->decimal('amount', 10, 2); // Montant total (calculé ou saisi)
            $table->string('proof_path')->nullable(); // Chemin du fichier uploadé

            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
