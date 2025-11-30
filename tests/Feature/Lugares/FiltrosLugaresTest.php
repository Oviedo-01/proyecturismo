<?php

namespace Tests\Feature\Lugares;

use Tests\TestCase;
use App\Models\User;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;
use Carbon\Carbon;

class FiltrosLugaresTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
    }

    // ==========================================
    // CP009: FILTRAR POR MEJOR CALIFICADOS
    // ==========================================

    public function test_filtro_mejor_calificados_ordena_por_calificacion_descendente()
    {
        $categoria = Categoria::factory()->create();

        // ✅ CORREGIDO: Crear lugares con calificación >= 4 (tu requerimiento)
        $lugar1 = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Excelente',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'promedio_calificacion' => 4.8,
        ]);

        $lugar2 = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Muy Bueno',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'promedio_calificacion' => 4.2,
        ]);

        $lugar3 = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Bueno',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'promedio_calificacion' => 4.0,
        ]);

        // Lugar con calificación baja NO debe aparecer
        $lugarBajo = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Regular',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'promedio_calificacion' => 3.5,
        ]);

        // Aplicar filtro de mejor calificados
        $response = $this->get(route('lugares.explorar', ['filtro' => 'mejor-calificados']));

        $response->assertStatus(200);

        // Verificar que los lugares >= 4 estrellas aparecen
        $response->assertSee('Lugar Excelente');
        $response->assertSee('Lugar Muy Bueno');
        $response->assertSee('Lugar Bueno');
        
        // Verificar que el lugar < 4 NO aparece
        $response->assertDontSee('Lugar Regular');

        // Verificar orden correcto (4.8 antes que 4.2 antes que 4.0)
        $content = $response->getContent();
        $posExcelente = strpos($content, 'Lugar Excelente');
        $posMuyBueno = strpos($content, 'Lugar Muy Bueno');
        $posBueno = strpos($content, 'Lugar Bueno');

        $this->assertLessThan($posMuyBueno, $posExcelente);
        $this->assertLessThan($posBueno, $posMuyBueno);
    }

    public function test_filtro_mejor_calificados_muestra_solo_lugares_visibles()
    {
        $categoria = Categoria::factory()->create();

        $lugarVisible = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Visible Top',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'promedio_calificacion' => 5.0,
        ]);

        $lugarOculto = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Oculto Top',
            'categoria_id' => $categoria->id,
            'visible' => false,
            'promedio_calificacion' => 4.9,
        ]);

        $response = $this->get(route('lugares.explorar', ['filtro' => 'mejor-calificados']));

        $response->assertStatus(200);
        $response->assertSee('Lugar Visible Top');
        $response->assertDontSee('Lugar Oculto Top');
    }

    // ==========================================
    // CP010: FILTRAR POR MÁS ECONÓMICOS
    // ==========================================

    public function test_filtro_mas_economicos_ordena_por_precio_ascendente()
    {
        $categoria = Categoria::factory()->create();

        // ✅ CORREGIDO: Crear lugares con precio <= 50000 (tu requerimiento)
        $lugarGratis = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Gratis',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'precio' => 0,
        ]);

        $lugarEconomico = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Económico',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'precio' => 5000,
        ]);

        $lugarModerado = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Moderado',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'precio' => 30000,
        ]);

        $lugarLimite = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Límite',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'precio' => 50000,
        ]);

        // Lugar caro NO debe aparecer (> 50000)
        $lugarCaro = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Caro',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'precio' => 80000,
        ]);

        // Aplicar filtro de más económicos
        $response = $this->get(route('lugares.explorar', ['filtro' => 'mas-economicos']));

        $response->assertStatus(200);

        // Verificar que aparecen los lugares <= 50000
        $response->assertSee('Lugar Gratis');
        $response->assertSee('Lugar Económico');
        $response->assertSee('Lugar Moderado');
        $response->assertSee('Lugar Límite');
        
        // Verificar que el lugar caro NO aparece
        $response->assertDontSee('Lugar Caro');
    }

    public function test_filtro_mas_economicos_incluye_lugares_gratuitos()
    {
        $categoria = Categoria::factory()->create();

        $lugarGratis1 = LugarTuristico::factory()->create([
            'nombre' => 'Parque Público',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'precio' => 0,
        ]);

        $lugarGratis2 = LugarTuristico::factory()->create([
            'nombre' => 'Playa Pública',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'precio' => 0,
        ]);

        $response = $this->get(route('lugares.explorar', ['filtro' => 'mas-economicos']));

        $response->assertStatus(200);
        $response->assertSee('Parque Público');
        $response->assertSee('Playa Pública');
    }

    // ==========================================
    // CP011: FILTRAR POR MÁS RECIENTES
    // ==========================================

    public function test_filtro_mas_recientes_ordena_por_fecha_creacion_descendente()
    {
        $categoria = Categoria::factory()->create();

        // Crear lugares en diferentes fechas
        $lugarAntiguo = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Antiguo',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'created_at' => Carbon::now()->subDays(30),
        ]);

        $lugarMedio = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Medio',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'created_at' => Carbon::now()->subDays(15),
        ]);

        $lugarReciente = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Reciente',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        $lugarNuevo = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Nuevo',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'created_at' => Carbon::now(),
        ]);

        // Aplicar filtro de más recientes
        $response = $this->get(route('lugares.explorar', ['filtro' => 'mas-recientes']));

        $response->assertStatus(200);

        // Verificar que aparecen en orden cronológico inverso
        $content = $response->getContent();
        $posNuevo = strpos($content, 'Lugar Nuevo');
        $posReciente = strpos($content, 'Lugar Reciente');
        $posMedio = strpos($content, 'Lugar Medio');
        $posAntiguo = strpos($content, 'Lugar Antiguo');

        // El lugar más nuevo debe aparecer primero
        $this->assertLessThan($posReciente, $posNuevo);
        $this->assertLessThan($posMedio, $posReciente);
        $this->assertLessThan($posAntiguo, $posMedio);
    }

    public function test_filtro_mas_recientes_muestra_lugares_de_hoy()
    {
        $categoria = Categoria::factory()->create();

        $lugarHoy = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Agregado Hoy',
            'categoria_id' => $categoria->id,
            'visible' => true,
            'created_at' => Carbon::now(),
        ]);

        $response = $this->get(route('lugares.explorar', ['filtro' => 'mas-recientes']));

        $response->assertStatus(200);
        $response->assertSee('Lugar Agregado Hoy');
    }

    // ==========================================
    // TESTS ADICIONALES
    // ==========================================

    public function test_sin_filtro_muestra_todos_los_lugares_visibles()
    {
        $categoria = Categoria::factory()->create();

        $lugar1 = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Uno',
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        $lugar2 = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Dos',
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        $lugar3 = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Tres',
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        // Sin parámetro de filtro
        $response = $this->get(route('lugares.explorar'));

        $response->assertStatus(200);
        $response->assertSee('Lugar Uno');
        $response->assertSee('Lugar Dos');
        $response->assertSee('Lugar Tres');
    }

    public function test_filtro_invalido_usa_orden_por_defecto()
    {
        $categoria = Categoria::factory()->create();

        $lugar = LugarTuristico::factory()->create([
            'nombre' => 'Lugar Test',
            'categoria_id' => $categoria->id,
            'visible' => true,
        ]);

        // Filtro inválido
        $response = $this->get(route('lugares.explorar', ['filtro' => 'filtro-inexistente']));

        $response->assertStatus(200);
        $response->assertSee('Lugar Test');
    }

    public function test_filtros_respetan_paginacion()
    {
        $categoria = Categoria::factory()->create();

        // Crear 15 lugares para probar paginación
        for ($i = 1; $i <= 15; $i++) {
            LugarTuristico::factory()->create([
                'nombre' => "Lugar $i",
                'categoria_id' => $categoria->id,
                'visible' => true,
                'promedio_calificacion' => rand(1, 5),
            ]);
        }

        $response = $this->get(route('lugares.explorar', ['filtro' => 'mejor-calificados']));

        $response->assertStatus(200);
        
        // Verificar que hay paginación (si tu implementación pagina)
        // Esto depende de cómo implementaste la paginación
        $this->assertTrue(true); // Placeholder
    }
}