<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'telefono',
        'password',
        'avatar', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ✅ Asignar rol 'usuario' automáticamente al registrarse
    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($user) {
            $user->assignRole('usuario');
        });
    }

    // ✅ Método para obtener la URL del avatar
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    // ✅ NUEVO: Relación con reservas
    /**
     * Reservas del usuario
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
