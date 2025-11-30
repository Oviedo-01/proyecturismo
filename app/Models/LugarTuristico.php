<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LugarTuristico extends Model
{
    use HasFactory;

    protected $table = 'lugar_turisticos';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'direccion',
        'latitud',
        'longitud',
        'horarios',
        'precio',
        'contacto',
        'visible',
        'promedio_calificacion',
        'categoria_id',
        'creador_id',
    ];

    protected $casts = [
        'latitud' => 'float',
        'longitud' => 'float',
        'precio' => 'decimal:2',
        'visible' => 'boolean',
        'promedio_calificacion' => 'float',
    ];

    // ==========================================
    // RELACIONES EXISTENTES
    // ==========================================
    
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id');
    }

    public function imagenes()
    {
        return $this->hasMany(Multimedia::class, 'lugar_id');
    }

    // ==========================================
    // RELACIONES PARA COMENTARIOS (CORREGIDO)
    // ==========================================
    
    /**
     * Comentarios aprobados del lugar
     */
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'lugar_id')
            ->where('estado', 'aprobada')
            ->latest();
    }

    /**
     * Todas las reseñas (sin filtrar por estado)
     */
    public function todasLasResenas()
    {
        return $this->hasMany(Comentario::class, 'lugar_id')
            ->latest();
    }

    /**
     * Reseñas pendientes de moderación
     */
    public function resenasPendientes()
    {
        return $this->hasMany(Comentario::class, 'lugar_id')
            ->where('estado', 'pendiente')
            ->latest();
    }

    // ==========================================
    // MÉTODOS PARA CALIFICACIONES
    // ==========================================
    
    /**
     * Calcular promedio de calificación (solo aprobadas)
     */
    public function calificacionPromedio()
    {
        $promedio = $this->comentarios()->avg('calificacion');
        return $promedio ? round($promedio, 1) : 0;
    }

    /**
     * Promedio redondeado (para estrellas)
     */
    public function calificacionPromedioRedondeado()
    {
        return round($this->calificacionPromedio());
    }

    /**
     * Total de comentarios aprobados
     */
    public function totalComentarios()
    {
        return $this->comentarios()->count();
    }

    /**
     * Distribución de calificaciones (cuántas de cada estrella)
     */
    public function distribucionCalificaciones()
    {
        $distribucion = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        
        $calificaciones = $this->comentarios()
            ->selectRaw('calificacion, COUNT(*) as total')
            ->groupBy('calificacion')
            ->pluck('total', 'calificacion');
        
        foreach ($calificaciones as $calificacion => $total) {
            $distribucion[$calificacion] = $total;
        }
        
        return $distribucion;
    }

    /**
     * Verificar si un usuario ya comentó este lugar
     */
    public function usuarioYaComento($userId)
    {
        return $this->todasLasResenas()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Actualizar el promedio de calificación en la BD
     */
    public function actualizarPromedioCalificacion()
    {
        $promedio = $this->calificacionPromedio();
        $this->update(['promedio_calificacion' => $promedio]);
    }

    /**
     * Obtener la imagen principal del lugar
     */
    public function imagenPrincipal()
    {
        $imagen = $this->imagenes()->first();
        return $imagen ? asset('storage/' . $imagen->url) : asset('tourism/img/package-1.jpg');
    }

    // ==========================================
    // MÉTODOS PARA MAPAS
    // ==========================================
    
    /**
     * Verificar si el lugar tiene coordenadas
     */
    public function tieneUbicacion()
    {
        return !is_null($this->latitud) && !is_null($this->longitud);
    }

    /**
     * Obtener URL de Google Maps
     */
    public function urlGoogleMaps()
    {
        if (!$this->tieneUbicacion()) {
            return null;
        }
        return "https://www.google.com/maps?q={$this->latitud},{$this->longitud}";
    }

    /**
     * Obtener coordenadas como array
     */
    public function coordenadas()
    {
        if (!$this->tieneUbicacion()) {
            return null;
        }
        return [
            'lat' => (float) $this->latitud,
            'lng' => (float) $this->longitud
        ];
    }
}