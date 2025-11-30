<?php

namespace Tests\Feature\Lugares;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\RolesSeeder;

class CrearLugarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Importante: cargar roles
        $this->seed(RolesSeeder::class);
    }

    /** @test */
    public function admin_puede_crear_lugar_turistico()
    {
        Storage::fake('public');

        // Crear admin
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Crear categoría
        $categoria = Categoria::factory()->create();

        // Datos del lugar
        $datos = [
            'nombre' => 'Parque Nacional',
            'descripcion' => 'Hermoso parque natural',
            'direccion' => 'Calle 123',
            'latitud' => 6.24,
            'longitud' => -75.56,
            'precio' => 10000,
            'horarios' => 'Lun-Dom 8am-6pm',
            'contacto' => '3115555555',
            'categoria_id' => $categoria->id,
            'imagenes' => [
                UploadedFile::fake()->image('foto1.jpg')
            ],
        ];

        // Ejecutar solicitud como admin
        $response = $this->actingAs($admin)->post('/lugares', $datos);

        // Debe redirigir
        $response->assertStatus(302);

        // Debe existir el lugar en DB
        $this->assertDatabaseHas('lugar_turisticos', [
            'nombre' => 'Parque Nacional',
            'direccion' => 'Calle 123',
        ]);
    }

    /** @test */
    public function usuario_normal_no_puede_crear_lugar_turistico()
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Lugar Test',
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($usuario)->post('/lugares', $datos);

        // Debe devolver 403 (prohibido)
        $response->assertStatus(403);
    }

    /** @test */
    public function no_se_puede_crear_lugar_sin_nombre()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => '',  // inválido
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($admin)->post('/lugares', $datos);

        // Debe fallar validación
        $response->assertSessionHasErrors('nombre');
    }
}
