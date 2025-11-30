<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'ubicacion',
        'capacidad',
        'precio',
        'estado',
        'categoria_id',
        'lugar_id',
        'creador_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'precio' => 'decimal:2',
    ];

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Categoría del evento
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Lugar turístico asociado
     */
    public function lugar()
    {
        return $this->belongsTo(LugarTuristico::class, 'lugar_id');
    }

    /**
     * Usuario creador del evento
     */
    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id');
    }

    /**
     * Reservas del evento
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    /**
     * Reservas confirmadas
     */
    public function reservasConfirmadas()
    {
        return $this->hasMany(Reserva::class)->where('estado', 'confirmada');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Scope: Solo eventos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope: Eventos próximos (futuros)
     */
    public function scopeProximos($query)
    {
        return $query->where('fecha_inicio', '>=', now());
    }

    /**
     * Scope: Eventos con cupos disponibles
     */
    public function scopeConCupos($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM reservas WHERE evento_id = eventos.id AND estado = "confirmada") < capacidad');
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    /**
     * Verificar si el evento es gratuito
     */
    public function esGratuito()
    {
        return $this->precio == 0;
    }

    /**
     * Obtener cupos disponibles
     */
    public function cuposDisponibles()
    {
        return $this->capacidad - $this->reservasConfirmadas()->count();
    }

    /**
     * Verificar si hay cupos disponibles
     */
    public function tieneCupos()
    {
        return $this->cuposDisponibles() > 0;
    }

    /**
     * Verificar si el evento ya pasó
     */
    public function haTerminado()
    {
        return $this->fecha_fin ? $this->fecha_fin->isPast() : false;
    }

    /**
     * Verificar si el evento está en curso
     */
    public function estaEnCurso()
    {
        $now = now();
        return $this->fecha_inicio <= $now && (!$this->fecha_fin || $this->fecha_fin >= $now);
    }

    /**
     * Verificar si un usuario ya está inscrito
     */
    public function usuarioEstaInscrito($userId)
    {
        return $this->reservas()
            ->where('user_id', $userId)
            ->where('estado', 'confirmada')
            ->exists();
    }

    /**
     * Obtener el porcentaje de ocupación
     */
    public function porcentajeOcupacion()
    {
        if ($this->capacidad == 0) return 0;
        
        $inscritos = $this->reservasConfirmadas()->count();
        return round(($inscritos / $this->capacidad) * 100, 1);
    }

    /**
     * Formato de fecha legible
     */
    public function fechaFormato()
    {
        if (!$this->fecha_inicio) return 'Fecha por confirmar';
        
        $inicio = $this->fecha_inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY [a las] h:mm A');
        
        if ($this->fecha_fin && !$this->fecha_inicio->isSameDay($this->fecha_fin)) {
            $fin = $this->fecha_fin->locale('es')->isoFormat('D [de] MMMM [de] YYYY [a las] h:mm A');
            return $inicio . ' hasta ' . $fin;
        }
        
        return $inicio;
    }
}