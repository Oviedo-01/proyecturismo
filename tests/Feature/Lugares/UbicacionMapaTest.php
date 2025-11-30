<?php

namespace Tests\Feature\Lugares;

use Tests\TestCase;
use App\Models\User;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;

class UbicacionMapaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
    }

    // ==========================================
    // CREAR LUGAR CON UBICACIÓN
    // ==========================================

    public function test_admin_puede_crear_lugar_con_coordenadas_validas()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Parque con Mapa',
            'descripcion' => 'Parque ubicado en el centro',
            'direccion' => 'Calle Principal 123',
            'latitud' => 8.748,
            'longitud' => -75.881,
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertRedirect(route('lugares.index'));

        // Verificar que se guardó con coordenadas
        $this->assertDatabaseHas('lugar_turisticos', [
            'nombre' => 'Parque con Mapa',
            'latitud' => 8.748,
            'longitud' => -75.881,
        ]);
    }

    public function test_puede_crear_lugar_sin_coordenadas()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Lugar Sin Mapa',
            'descripcion' => 'Lugar sin ubicación específica',
            'categoria_id' => $categoria->id,
            // Sin latitud ni longitud
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertRedirect(route('lugares.index'));

        // Verificar que se creó sin coordenadas (null)
        $lugar = LugarTuristico::where('nombre', 'Lugar Sin Mapa')->first();
        $this->assertNull($lugar->latitud);
        $this->assertNull($lugar->longitud);
    }

    // ==========================================
    // VALIDACIÓN DE COORDENADAS
    // ==========================================

    public function test_rechaza_latitud_fuera_de_rango()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Latitud inválida (> 90)
        $datos = [
            'nombre' => 'Lugar Inválido',
            'categoria_id' => $categoria->id,
            'latitud' => 95.0,
            'longitud' => -75.881,
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        // Debe fallar la validación
        $response->assertSessionHasErrors(['latitud']);

        // Latitud inválida (< -90)
        $datos['latitud'] = -95.0;

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertSessionHasErrors(['latitud']);
    }

    public function test_rechaza_longitud_fuera_de_rango()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Longitud inválida (> 180)
        $datos = [
            'nombre' => 'Lugar Inválido',
            'categoria_id' => $categoria->id,
            'latitud' => 8.748,
            'longitud' => 190.0,
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertSessionHasErrors(['longitud']);

        // Longitud inválida (< -180)
        $datos['longitud'] = -190.0;

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertSessionHasErrors(['longitud']);
    }

    public function test_acepta_coordenadas_en_limites_validos()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Coordenadas en los límites exactos
        $coordenadasValidas = [
            ['latitud' => 90, 'longitud' => 180, 'nombre' => 'Polo Norte Límite'],
            ['latitud' => -90, 'longitud' => -180, 'nombre' => 'Polo Sur Límite'],
            ['latitud' => 0, 'longitud' => 0, 'nombre' => 'Ecuador Meridiano'],
        ];

        foreach ($coordenadasValidas as $coords) {
            $datos = [
                'nombre' => $coords['nombre'],
                'categoria_id' => $categoria->id,
                'latitud' => $coords['latitud'],
                'longitud' => $coords['longitud'],
            ];

            $response = $this->actingAs($admin)
                ->post(route('lugares.store'), $datos);

            $response->assertRedirect(route('lugares.index'));

            $this->assertDatabaseHas('lugar_turisticos', [
                'nombre' => $coords['nombre'],
                'latitud' => $coords['latitud'],
                'longitud' => $coords['longitud'],
            ]);
        }
    }

    // ==========================================
    // EDITAR UBICACIÓN
    // ==========================================

    public function test_admin_puede_actualizar_coordenadas_de_lugar_existente()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Crear lugar sin coordenadas
        $lugar = LugarTuristico::factory()->create([
            'nombre' => 'Lugar a Actualizar',
            'categoria_id' => $categoria->id,
            'latitud' => null,
            'longitud' => null,
        ]);

        // Actualizar con coordenadas
        $datos = [
            'nombre' => 'Lugar a Actualizar',
            'categoria_id' => $categoria->id,
            'latitud' => 10.391,
            'longitud' => -75.479,
        ];

        $response = $this->actingAs($admin)
            ->put(route('lugares.update', $lugar->id), $datos);

        $response->assertRedirect(route('lugares.index'));

        // Verificar que se actualizaron las coordenadas
        $this->assertDatabaseHas('lugar_turisticos', [
            'id' => $lugar->id,
            'latitud' => 10.391,
            'longitud' => -75.479,
        ]);
    }

    public function test_admin_puede_cambiar_coordenadas_existentes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Crear lugar con coordenadas iniciales
        $lugar = LugarTuristico::factory()->create([
            'nombre' => 'Lugar con Ubicación',
            'categoria_id' => $categoria->id,
            'latitud' => 5.0,
            'longitud' => -70.0,
        ]);

        // Cambiar las coordenadas
        $datos = [
            'nombre' => 'Lugar con Ubicación',
            'categoria_id' => $categoria->id,
            'latitud' => 8.748,
            'longitud' => -75.881,
        ];

        $response = $this->actingAs($admin)
            ->put(route('lugares.update', $lugar->id), $datos);

        $response->assertRedirect(route('lugares.index'));

        // Verificar las nuevas coordenadas
        $lugar->refresh();
        $this->assertEquals(8.748, $lugar->latitud);
        $this->assertEquals(-75.881, $lugar->longitud);
    }

    public function test_admin_puede_eliminar_coordenadas_de_lugar()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Crear lugar con coordenadas
        $lugar = LugarTuristico::factory()->create([
            'nombre' => 'Lugar con Mapa',
            'categoria_id' => $categoria->id,
            'latitud' => 8.748,
            'longitud' => -75.881,
        ]);

        // Eliminar coordenadas (enviar null)
        $datos = [
            'nombre' => 'Lugar con Mapa',
            'categoria_id' => $categoria->id,
            'latitud' => null,
            'longitud' => null,
        ];

        $response = $this->actingAs($admin)
            ->put(route('lugares.update', $lugar->id), $datos);

        $response->assertRedirect(route('lugares.index'));

        // Verificar que se eliminaron las coordenadas
        $lugar->refresh();
        $this->assertNull($lugar->latitud);
        $this->assertNull($lugar->longitud);
    }

    // ==========================================
    // MOSTRAR MAPA EN VISTA PÚBLICA
    // ==========================================

    public function test_lugar_con_coordenadas_muestra_mapa()
    {
        $categoria = Categoria::factory()->create();

        $lugar = LugarTuristico::factory()->create([
            'nombre' => 'Lugar con Mapa Público',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'latitud' => 8.748,
            'longitud' => -75.881,
        ]);

        $response = $this->get(route('lugar.mostrar', $lugar->id));

        $response->assertStatus(200);
        $response->assertSee('Ubicación en el Mapa');
        $response->assertSee('id="map"', false); // false = buscar HTML sin escapar
    }

    public function test_lugar_sin_coordenadas_no_muestra_mapa()
    {
        $categoria = Categoria::factory()->create();

        $lugar = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Sin Mapa',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'latitud' => null,
            'longitud' => null,
        ]);

        $response = $this->get(route('lugar.mostrar', $lugar->id));

        $response->assertStatus(200);
        $response->assertDontSee('Ubicación en el Mapa');
        $response->assertDontSee('id="map"', false);
    }
}