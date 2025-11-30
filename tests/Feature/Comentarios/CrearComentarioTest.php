<?php

namespace Tests\Feature\Comentarios;

use Tests\TestCase;
use App\Models\User;
use App\Models\LugarTuristico;
use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;

class CrearComentarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_usuario_autenticado_puede_crear_comentario()
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'creador_id' => $usuario->id,
            'visible' => true,
        ]);

        $datos = [
            'calificacion' => 5,
            'comentario' => 'Excelente lugar, muy recomendado para toda la familia',
        ];

        $response = $this->actingAs($usuario)
            ->post(route('comentarios.store', $lugar->id), $datos);

        // Verificar redirección exitosa
        $response->assertRedirect();

        // ✅ Verificar que se creó en la base de datos
        $this->assertDatabaseHas('resenas', [
            'comentario' => 'Excelente lugar, muy recomendado para toda la familia',
            'calificacion' => 5,
            'user_id' => $usuario->id,
            'lugar_id' => $lugar->id,
            'estado' => 'aprobada',
        ]);
    }

    public function test_visitante_no_puede_crear_comentario()
    {
        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        $datos = [
            'calificacion' => 5,
            'comentario' => 'Intento sin login',
        ];

        $response = $this->post(route('comentarios.store', $lugar->id), $datos);

        // Debe redirigir al login
        $response->assertRedirect(route('login'));
        
        // Verificar que NO se creó el comentario
        $this->assertDatabaseMissing('resenas', [
            'comentario' => 'Intento sin login',
        ]);
    }

    public function test_no_puede_crear_comentario_sin_calificacion()
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        $datos = [
            'comentario' => 'Comentario sin calificación',
            // Falta 'calificacion'
        ];

        $response = $this->actingAs($usuario)
            ->post(route('comentarios.store', $lugar->id), $datos);

        // Debe fallar la validación
        $response->assertSessionHasErrors(['calificacion']);
        
        // Verificar que NO se creó
        $this->assertDatabaseMissing('resenas', [
            'comentario' => 'Comentario sin calificación',
        ]);
    }

    public function test_no_puede_crear_comentario_muy_corto()
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        $datos = [
            'calificacion' => 4,
            'comentario' => 'Corto', // Menos de 10 caracteres
        ];

        $response = $this->actingAs($usuario)
            ->post(route('comentarios.store', $lugar->id), $datos);

        // Debe fallar la validación (mínimo 10 caracteres)
        $response->assertSessionHasErrors(['comentario']);
        
        // Verificar que NO se creó
        $this->assertDatabaseMissing('resenas', [
            'comentario' => 'Corto',
        ]);
    }
}