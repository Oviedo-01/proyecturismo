<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $table = 'resenas';
    
    protected $fillable = [
        'user_id',
        'lugar_id',
        'calificacion',
        'comentario',
        'estado'
    ];

    // ==========================================
    // RELACIONES
    // ==========================================
    
    /**
     * Relación con el usuario que hizo el comentario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el lugar turístico
     */
    public function lugar()
    {
        return $this->belongsTo(LugarTuristico::class, 'lugar_id');
    }

    // ==========================================
    // SCOPES (Consultas reutilizables)
    // ==========================================
    
    /**
     * Scope para obtener solo comentarios aprobados
     */
    public function scopeAprobados($query)
    {
        return $query->where('estado', 'aprobada');
    }

    /**
     * Scope para obtener comentarios pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para obtener comentarios rechazados
     */
    public function scopeRechazados($query)
    {
        return $query->where('estado', 'rechazada');
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================
    
    /**
     * Verificar si el comentario está aprobado
     * @return bool
     */
    public function estaAprobado()
    {
        return $this->estado === 'aprobada';
    }

    /**
     * Verificar si el comentario está pendiente
     * @return bool
     */
    public function estaPendiente()
    {
        return $this->estado === 'pendiente';
    }

    /**
     * Aprobar el comentario
     */
    public function aprobar()
    {
        $this->update(['estado' => 'aprobada']);
        $this->lugar->actualizarPromedioCalificacion();
    }

    /**
     * Rechazar el comentario
     */
    public function rechazar()
    {
        $this->update(['estado' => 'rechazada']);
        $this->lugar->actualizarPromedioCalificacion();
    }
}