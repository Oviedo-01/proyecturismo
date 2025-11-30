<?php

namespace Tests\Feature\Lugares;

use Tests\TestCase;
use App\Models\User;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;

class EliminarLugarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Cargar roles
        $this->seed(RolesSeeder::class);
    }

    /** @test */
    public function admin_puede_eliminar_lugar_turistico()
    {
        // Crear admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Crear categoría
        $categoria = Categoria::factory()->create();

        // Crear lugar
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'creador_id' => $admin->id,
        ]);

        // Petición de eliminación
        $response = $this->actingAs($admin)->delete('/lugares/' . $lugar->id);

        // Debe redirigir
        $response->assertStatus(302);

        // No debe existir ya en la BD
        $this->assertDatabaseMissing('lugar_turisticos', [
            'id' => $lugar->id,
        ]);
    }

    /** @test */
    public function usuario_normal_no_puede_eliminar_lugar()
    {
        // Crear usuario normal
        $user = User::factory()->create();
        $user->assignRole('usuario');

        // Crear categoría y lugar
        $categoria = Categoria::factory()->create();

        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'creador_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete('/lugares/' . $lugar->id);

        // Debe devolver prohibido
        $response->assertStatus(403);

        // El lugar debe seguir existiendo
        $this->assertDatabaseHas('lugar_turisticos', [
            'id' => $lugar->id,
        ]);
    }

    /** @test */
    public function no_se_puede_eliminar_un_lugar_inexistente()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->delete('/lugares/99999');

        // Dependiendo de tu controlador: 404 o redirección
        $response->assertStatus(404);
    }
}
