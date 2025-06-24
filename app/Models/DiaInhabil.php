<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiaInhabil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dias_inhabiles';

    protected $fillable = ['fecha', 'descripcion'];
    
    protected $casts = [
        'fecha' => 'date',
    ];
}
