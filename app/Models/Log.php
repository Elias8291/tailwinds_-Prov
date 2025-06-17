<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'message',
        'context',
        'channel',
        'user_id',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con el usuario que generó el log
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por nivel
     */
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope para filtrar por canal
     */
    public function scopeChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope para filtrar por fecha
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Obtener el color del badge según el nivel
     */
    public function getLevelColorAttribute()
    {
        return match($this->level) {
            'error', 'emergency', 'alert', 'critical' => 'red',
            'warning' => 'yellow',
            'notice', 'info' => 'blue',
            'debug' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Obtener el icono según el nivel
     */
    public function getLevelIconAttribute()
    {
        return match($this->level) {
            'error', 'emergency', 'alert', 'critical' => 'exclamation-triangle',
            'warning' => 'exclamation-circle',
            'notice', 'info' => 'information-circle',
            'debug' => 'code',
            default => 'clipboard-list',
        };
    }
}
