<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoPais extends Model
{
    use HasFactory;

    protected $table = 'catalogo_paises';
    
    protected $fillable = [
        'nombre'
    ];
}
