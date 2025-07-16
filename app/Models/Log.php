<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'level',
        'message',
        'channel',
        'context',
        'user_id',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];
} 