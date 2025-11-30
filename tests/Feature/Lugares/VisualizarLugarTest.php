<?php

namespace Tests\Feature\Lugares;

use Tests\TestCase;
use App\Models\User;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;

class VisualizarLugarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    public function test_cualquier_usuario_puede_ver_detalles_de_lugar()
    {
        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'nombre' => 'Parque Central',
            'descripcion' => 'Hermoso parque',
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        // ✅ CORREGIDO: Usa la ruta pública /lugar/{id} (sin la "s")
        $response = $this->get(route('lugar.mostrar', $lugar->id));

        $response->assertStatus(200);
        $response->assertSee('Parque Central');
        $response->assertSee('Hermoso parque');
    }

    public function test_usuario_autenticado_puede_ver_lugar()
    {
        $user = User::factory()->create();
        $user->assignRole('usuario');

        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        // ✅ CORREGIDO: Usa route() con el nombre correcto
        $response = $this->actingAs($user)->get(route('lugar.mostrar', $lugar->id));

        $response->assertStatus(200);
    }

    public function test_error_404_si_lugar_no_existe()
    {
        // ✅ CORREGIDO: Usa la ruta pública correcta
        $response = $this->get('/lugar/99999');

        $response->assertStatus(404);
    }

    public function test_admin_puede_ver_lugar_en_panel_admin()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();
        
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        // Admin usa la ruta protegida /lugares/{id} (con "s")
        $response = $this->actingAs($admin)->get(route('lugares.show', $lugar->id));

        $response->assertStatus(200);
    }

    public function test_lugares_no_visibles_no_aparecen_en_listado_publico()
    {
        $categoria = Categoria::factory()->create();
        
        $lugarVisible = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Visible',
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        $lugarOculto = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Oculto',
            'categoria_id' => $categoria->id,
            'visible' => false,
        ]);

        $response = $this->get(route('lugares.explorar'));

        $response->assertStatus(200);
        $response->assertSee('Lugar Visible');
        $response->assertDontSee('Lugar Oculto');
    }
}