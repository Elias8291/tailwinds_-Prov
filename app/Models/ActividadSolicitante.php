<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActividadSolicitante extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'actividad_solicitante';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tramite_id',
        'actividad_id',
    ];

    /**
     * Get the tramite that owns the actividad solicitante.
     */
    public function tramite(): BelongsTo
    {
        return $this->belongsTo(Tramite::class);
    }

    /**
     * Get the actividad that owns the actividad solicitante.
     */
    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }
}
