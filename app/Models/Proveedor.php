<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proveedor';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'pv';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pv',
        'solicitante_id',
        'fecha_registro',
        'fecha_vencimiento',
        'estado',
        'observaciones',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_registro' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    /**
     * Get the solicitante that owns the proveedor.
     */
    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }
} 