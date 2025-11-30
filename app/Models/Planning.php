<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Planning extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     * C'est cette liste qui corrige ton erreur "MassAssignmentException".
     */
    protected $fillable = [
        'user_id',      // L'éducateur concerné
        'creator_id',   // Qui a créé l'event (la compta)
        'title',
        'start_at',
        'end_at',
        'type',
        'description',
    ];

    /**
     * Les attributs qui doivent être convertis en objets Date (Carbon).
     * Indispensable pour que FullCalendar reçoive le bon format JSON.
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // --- RELATIONS ---

    /**
     * L'éducateur à qui appartient ce créneau.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * La personne (Compta/Admin) qui a créé ce créneau.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}