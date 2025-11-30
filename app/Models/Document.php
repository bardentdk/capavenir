<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'path',
        'type', // Ex: 'mdph', 'jugement', 'ppa'...
    ];

    /**
     * Le bénéficiaire à qui appartient ce document.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}