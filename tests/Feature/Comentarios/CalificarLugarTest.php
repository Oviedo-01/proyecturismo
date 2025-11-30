<?php

namespace Tests\Feature\Comentarios;

use Tests\TestCase;
use App\Models\User;
use App\Models\LugarTuristico;
use App\Models\Categoria;
use App\Models\Comentario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;

class CalificarLugarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_calificacion_actualiza_promedio_del_lugar()
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'creador_id' => $usuario->id,
            'promedio_calificacion' => 0,
        ]);

        // Crear comentario DIRECTAMENTE en BD (sin pasar por controlador)
        Comentario::create([
            'user_id' => $usuario->id,
            'lugar_id' => $lugar->id,
            'calificacion' => 5,
            'comentario' => 'Excelente, cinco estrellas',
            'estado' => 'aprobada',
        ]);

        // Actualizar promedio manualmente
        $lugar->actualizarPromedioCalificacion();
        $lugar->refresh();

        $this->assertEquals(5.0, $lugar->promedio_calificacion);
    }

    public function test_promedio_se_calcula_correctamente_con_multiples_calificaciones()
    {
        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
        ]);

        // Usuario 1: calificación 5
        $user1 = User::factory()->create();
        $user1->assignRole('usuario');
        
        Comentario::create([
            'user_id' => $user1->id,
            'lugar_id' => $lugar->id,
            'calificacion' => 5,
            'comentario' => 'Excelente lugar',
            'estado' => 'aprobada',
        ]);

        // Usuario 2: calificación 3
        $user2 = User::factory()->create();
        $user2->assignRole('usuario');
        
        Comentario::create([
            'user_id' => $user2->id,
            'lugar_id' => $lugar->id,
            'calificacion' => 3,
            'comentario' => 'Lugar regular',
            'estado' => 'aprobada',
        ]);

        // Actualizar promedio
        $lugar->actualizarPromedioCalificacion();
        $lugar->refresh();

        // Promedio esperado: (5 + 3) / 2 = 4.0
        $this->assertEquals(4.0, $lugar->promedio_calificacion);
    }
}