<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_padre',
    ];

    // Relación para subcategorías
    public function subcategorias()
    {
        return $this->hasMany(Categoria::class, 'categoria_padre');
    }

    // Relación para la categoría padre
    public function padre()
    {
        return $this->belongsTo(Categoria::class, 'categoria_padre');
    }
}

