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
    // RELACIONES PARA COMENTARIOS
    // ==========================================
    
    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'lugar_id')
            ->where('estado', 'aprobada')
            ->latest();
    }

    public function todasLasResenas()
    {
        return $this->hasMany(Comentario::class, 'lugar_id')->latest();
    }

    // ==========================================
    // MÉTODOS PARA CALIFICACIONES
    // ==========================================
    
    public function calificacionPromedio()
    {
        $promedio = $this->comentarios()->avg('calificacion');
        return $promedio ? round($promedio, 1) : 0;
    }

    public function calificacionPromedioRedondeado()
    {
        return round($this->calificacionPromedio());
    }

    public function totalComentarios()
    {
        return $this->comentarios()->count();
    }

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

    public function usuarioYaComento($userId)
    {
        return $this->todasLasResenas()
            ->where('user_id', $userId)
            ->exists();
    }

    public function actualizarPromedioCalificacion()
    {
        $promedio = $this->calificacionPromedio();
        $this->update(['promedio_calificacion' => $promedio]);
    }

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