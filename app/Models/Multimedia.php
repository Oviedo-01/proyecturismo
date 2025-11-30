<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multimedia extends Model
{
    use HasFactory;

    protected $table = 'multimedia';
    protected $primaryKey = 'id';  // ✅ CORREGIDO
    public $timestamps = true;

    protected $fillable = [
        'tipo',
        'url',
        'formato',
        'tamano',
        'descripcion',
        'lugar_id',   // ✅ CORREGIDO (era id_lugar)
        'evento_id',  // ✅ CORREGIDO (era id_evento)
    ];

    public function lugar()
    {
        return $this->belongsTo(LugarTuristico::class, 'lugar_id');  // ✅ CORREGIDO
    }

   // public function evento()
    //{
    //  return $this->belongsTo(Evento::class, 'evento_id');
    //}
}