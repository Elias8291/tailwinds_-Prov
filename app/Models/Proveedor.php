<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'pv';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pv',
        'solicitante_id',
        'fecha_registro',
        'fecha_vencimiento',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'fecha_vencimiento' => 'datetime'
    ];

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class, 'solicitante_id');
    }

    public function tramites()
    {
        return $this->hasMany(Tramite::class, 'solicitante_id', 'solicitante_id');
    }

    public function detallesTramite()
    {
        return $this->hasManyThrough(
            DetalleTramite::class,
            Tramite::class,
            'solicitante_id', // Foreign key on tramite table
            'tramite_id', // Foreign key on detalle_tramite table
            'solicitante_id', // Local key on proveedor table
            'id' // Local key on tramite table
        );
    }

    public function getDiasParaVencerAttribute()
    {
        if ($this->fecha_vencimiento) {
            return Carbon::now()->diffInDays($this->fecha_vencimiento, false);
        }
        return null;
    }

    public function getEstaProximoAVencerAttribute()
    {
        return $this->dias_para_vencer <= 7 && $this->dias_para_vencer >= 0;
    }
} 