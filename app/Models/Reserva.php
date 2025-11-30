<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evento_id',
        'estado',
        'notas',
        'fecha_reserva',
    ];

    protected $casts = [
        'fecha_reserva' => 'datetime',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Usuario que hizo la reserva
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Evento reservado
     */
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope: Reservas confirmadas
     */
    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    /**
     * Scope: Reservas canceladas
     */
    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    /**
     * Scope: Reservas pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    /**
     * Verificar si la reserva está confirmada
     */
    public function estaConfirmada()
    {
        return $this->estado === 'confirmada';
    }

    /**
     * Cancelar la reserva
     */
    public function cancelar()
    {
        $this->update(['estado' => 'cancelada']);
    }

    /**
     * Confirmar la reserva
     */
    public function confirmar()
    {
        $this->update(['estado' => 'confirmada']);
    }
}