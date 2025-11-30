<?php

namespace Tests\Feature\Lugares;

use Tests\TestCase;
use App\Models\User;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EditarLugarTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesSeeder::class);
    }

    /** @test */
    public function admin_puede_editar_lugar_turistico()
    {
        $admin = User::factory()->create()->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'creador_id' => $admin->id,
            'nombre' => 'Original'
        ]);

        $response = $this->actingAs($admin)->put('/lugares/' . $lugar->id, [
            'nombre' => 'Editado',
            'categoria_id' => $categoria->id,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('lugar_turisticos', [
            'id' => $lugar->id,
            'nombre' => 'Editado'
        ]);
    }

    /** @test */
    public function usuario_normal_no_puede_editar_lugar()
    {
        $user = User::factory()->create()->assignRole('usuario');

        $categoria = Categoria::factory()->create();

        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'creador_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/lugares/' . $lugar->id, [
            'nombre' => 'Editado'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function no_se_puede_editar_sin_nombre()
    {
        $admin = User::factory()->create()->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'creador_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->put('/lugares/' . $lugar->id, [
            'nombre' => '',
            'categoria_id' => $categoria->id,
        ]);

        $response->assertSessionHasErrors(['nombre']);
    }
}
