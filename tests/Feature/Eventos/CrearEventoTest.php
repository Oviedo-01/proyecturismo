<?php

namespace Tests\Feature\Eventos;

use Tests\TestCase;
use App\Models\User;
use App\Models\Evento;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;
use Carbon\Carbon;

class CrearEventoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
    }

    // ==========================================
    // RF7: CREAR EVENTO TURÍSTICO
    // ==========================================

    public function test_admin_puede_crear_evento_con_todos_los_campos()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
        ]);

        $datos = [
            'nombre' => 'Tour Guiado por el Centro',
            'descripcion' => 'Recorrido histórico por el centro de la ciudad',
            'fecha_inicio' => Carbon::now()->addDays(7)->format('Y-m-d\TH:i'),
            'fecha_fin' => Carbon::now()->addDays(7)->addHours(3)->format('Y-m-d\TH:i'),
            'ubicacion' => 'Plaza Principal',
            'capacidad' => 30,
            'precio' => 25000,
            'categoria_id' => $categoria->id,
            'lugar_id' => $lugar->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('eventos.store'), $datos);

        $response->assertRedirect(route('eventos.index'));

        // Verificar que se guardó en BD con todos los campos
        $this->assertDatabaseHas('eventos', [
            'nombre' => 'Tour Guiado por el Centro',
            'descripcion' => 'Recorrido histórico por el centro de la ciudad',
            'ubicacion' => 'Plaza Principal',
            'capacidad' => 30,
            'precio' => 25000,
            'estado' => 'activo',
            'categoria_id' => $categoria->id,
            'lugar_id' => $lugar->id,
        ]);
    }

    public function test_admin_puede_crear_evento_gratuito()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Evento Gratuito de Cultura',
            'descripcion' => 'Evento cultural sin costo',
            'fecha_inicio' => Carbon::now()->addDays(5)->format('Y-m-d\TH:i'),
            'capacidad' => 100,
            'precio' => 0, // Gratuito
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('eventos.store'), $datos);

        $response->assertRedirect(route('eventos.index'));

        $this->assertDatabaseHas('eventos', [
            'nombre' => 'Evento Gratuito de Cultura',
            'precio' => 0,
        ]);
    }

    public function test_evento_requiere_campos_obligatorios()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Intentar crear sin campos obligatorios
        $datos = [];

        $response = $this->actingAs($admin)
            ->post(route('eventos.store'), $datos);

        // Debe fallar validación
        $response->assertSessionHasErrors([
            'nombre',
            'fecha_inicio',
            'capacidad',
            'precio',
            'categoria_id'
        ]);
    }

    public function test_fecha_fin_debe_ser_posterior_a_fecha_inicio()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Evento con Fechas Inválidas',
            'fecha_inicio' => Carbon::now()->addDays(7)->format('Y-m-d\TH:i'),
            'fecha_fin' => Carbon::now()->addDays(5)->format('Y-m-d\TH:i'), // Anterior a inicio
            'capacidad' => 50,
            'precio' => 10000,
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('eventos.store'), $datos);

        // Debe fallar validación
        $response->assertSessionHasErrors(['fecha_fin']);
    }

    public function test_capacidad_debe_ser_mayor_a_cero()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Evento Sin Capacidad',
            'fecha_inicio' => Carbon::now()->addDays(3)->format('Y-m-d\TH:i'),
            'capacidad' => 0, // Inválido
            'precio' => 5000,
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('eventos.store'), $datos);

        $response->assertSessionHasErrors(['capacidad']);
    }

    public function test_usuario_normal_no_puede_crear_eventos()
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Intento de Usuario',
            'fecha_inicio' => Carbon::now()->addDays(1)->format('Y-m-d\TH:i'),
            'capacidad' => 20,
            'precio' => 0,
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($usuario)
            ->post(route('eventos.store'), $datos);

        // Debe ser rechazado (403 o redirect)
        $this->assertContains($response->status(), [302, 403]);

        // Verificar que NO se creó
        $this->assertDatabaseMissing('eventos', [
            'nombre' => 'Intento de Usuario',
        ]);
    }

    public function test_visitante_no_puede_crear_eventos()
    {
        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Intento Sin Login',
            'fecha_inicio' => Carbon::now()->addDays(1)->format('Y-m-d\TH:i'),
            'capacidad' => 10,
            'precio' => 0,
            'categoria_id' => $categoria->id,
        ];

        $response = $this->post(route('eventos.store'), $datos);

        // Debe redirigir al login
        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('eventos', [
            'nombre' => 'Intento Sin Login',
        ]);
    }

    // ==========================================
    // TESTS ADICIONALES
    // ==========================================

    public function test_evento_puede_asociarse_a_lugar_turistico()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();
        $lugar = LugarTuristico::factory()->create([
            'categoria_id' => $categoria->id,
            'nombre' => 'Parque Central',
        ]);

        $datos = [
            'nombre' => 'Evento en Parque',
            'fecha_inicio' => Carbon::now()->addDays(2)->format('Y-m-d\TH:i'),
            'capacidad' => 40,
            'precio' => 0,
            'categoria_id' => $categoria->id,
            'lugar_id' => $lugar->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('eventos.store'), $datos);

        $response->assertRedirect();

        // Verificar asociación
        $evento = Evento::where('nombre', 'Evento en Parque')->first();
        $this->assertNotNull($evento);
        $this->assertEquals($lugar->id, $evento->lugar_id);
    }

    public function test_evento_se_crea_con_estado_activo_por_defecto()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Evento Nuevo',
            'fecha_inicio' => Carbon::now()->addDays(1)->format('Y-m-d\TH:i'),
            'capacidad' => 25,
            'precio' => 15000,
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('eventos.store'), $datos);

        $response->assertRedirect();

        // Verificar que está activo
        $evento = Evento::where('nombre', 'Evento Nuevo')->first();
        $this->assertEquals('activo', $evento->estado);
    }

    public function test_evento_guarda_creador_correctamente()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Evento Test Creador',
            'fecha_inicio' => Carbon::now()->addDays(4)->format('Y-m-d\TH:i'),
            'capacidad' => 15,
            'precio' => 8000,
            'categoria_id' => $categoria->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('eventos.store'), $datos);

        $response->assertRedirect();

        // Verificar que el creador es el admin
        $evento = Evento::where('nombre', 'Evento Test Creador')->first();
        $this->assertEquals($admin->id, $evento->creador_id);
    }
}
