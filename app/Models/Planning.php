<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <--- C'est cet import qui manquait !

class Planning extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'creator_id',   // Qui a créé l'event
        // 'user_id',   // On ne l'utilise plus directement car on est en ManyToMany, mais on peut le garder pour historique
        'title',
        'start_at',
        'end_at',
        'type',
        'description',
    ];

    /**
     * Les attributs qui doivent être convertis en objets Date (Carbon).
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // --- RELATIONS ---

    /**
     * Les participants à cet événement (Relation Many-to-Many).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'planning_user');
    }

    /**
     * La personne (Compta/Admin) qui a créé ce créneau.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}