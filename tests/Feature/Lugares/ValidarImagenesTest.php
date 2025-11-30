<?php

namespace Tests\Feature\Lugares;

use Tests\TestCase;
use App\Models\User;
use App\Models\Categoria;
use App\Models\LugarTuristico;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\RolesSeeder;

class ValidarImagenesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        Storage::fake('public');
    }

    public function test_admin_puede_subir_una_imagen_al_crear_lugar()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $imagen = UploadedFile::fake()->image('lugar1.jpg', 800, 600)->size(1024); // 1MB

        $datos = [
            'nombre' => 'Lugar con una imagen',
            'descripcion' => 'Descripción del lugar',
            'direccion' => 'Calle Principal 123',
            'categoria_id' => $categoria->id,
            'precio' => 5000,
            'imagenes' => [$imagen],
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertRedirect(route('lugares.index'));
        
        // Verificar que el lugar se creó
        $this->assertDatabaseHas('lugar_turisticos', [
            'nombre' => 'Lugar con una imagen',
        ]);

        // Verificar que la imagen se guardó
        $lugar = LugarTuristico::where('nombre', 'Lugar con una imagen')->first();
        $this->assertCount(1, $lugar->imagenes);
        
        Storage::disk('public')->assertExists($lugar->imagenes->first()->url);
    }

    public function test_admin_puede_subir_multiples_imagenes_al_crear_lugar()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $imagenes = [
            UploadedFile::fake()->image('lugar1.jpg')->size(1024),
            UploadedFile::fake()->image('lugar2.jpg')->size(1024),
            UploadedFile::fake()->image('lugar3.jpg')->size(1024),
        ];

        $datos = [
            'nombre' => 'Lugar con múltiples imágenes',
            'descripcion' => 'Descripción del lugar',
            'categoria_id' => $categoria->id,
            'imagenes' => $imagenes,
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertRedirect(route('lugares.index'));

        // Verificar que se guardaron las 3 imágenes
        $lugar = LugarTuristico::where('nombre', 'Lugar con múltiples imágenes')->first();
        $this->assertCount(3, $lugar->imagenes);

        // Verificar que todas las imágenes existen en storage
        foreach ($lugar->imagenes as $imagen) {
            Storage::disk('public')->assertExists($imagen->url);
        }
    }

    public function test_no_permite_subir_mas_de_5_imagenes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Intentar subir 6 imágenes (excede el límite)
        $imagenes = [
            UploadedFile::fake()->image('img1.jpg')->size(500),
            UploadedFile::fake()->image('img2.jpg')->size(500),
            UploadedFile::fake()->image('img3.jpg')->size(500),
            UploadedFile::fake()->image('img4.jpg')->size(500),
            UploadedFile::fake()->image('img5.jpg')->size(500),
            UploadedFile::fake()->image('img6.jpg')->size(500),
        ];

        $datos = [
            'nombre' => 'Lugar con exceso de imágenes',
            'descripcion' => 'Descripción',
            'categoria_id' => $categoria->id,
            'imagenes' => $imagenes,
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        // Debe fallar la validación
        $response->assertSessionHasErrors(['imagenes']);
    }

    public function test_rechaza_archivos_que_no_son_imagenes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Intentar subir un archivo PDF en lugar de imagen
        $archivo = UploadedFile::fake()->create('documento.pdf', 1024);

        $datos = [
            'nombre' => 'Lugar con archivo inválido',
            'descripcion' => 'Descripción',
            'categoria_id' => $categoria->id,
            'imagenes' => [$archivo],
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        // Debe fallar la validación
        $response->assertSessionHasErrors(['imagenes.0']);
    }

    public function test_rechaza_imagenes_mayores_a_2mb()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Imagen de 3MB (excede el límite de 2MB)
        $imagenGrande = UploadedFile::fake()->image('imagen_grande.jpg')->size(3072); // 3MB

        $datos = [
            'nombre' => 'Lugar con imagen grande',
            'descripcion' => 'Descripción',
            'categoria_id' => $categoria->id,
            'imagenes' => [$imagenGrande],
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        // Debe fallar la validación
        $response->assertSessionHasErrors(['imagenes.0']);
    }

    public function test_acepta_formatos_jpg_jpeg_png()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        // Probar los 3 formatos permitidos
        $imagenes = [
            UploadedFile::fake()->image('imagen.jpg')->size(1024),
            UploadedFile::fake()->image('imagen.jpeg')->size(1024),
            UploadedFile::fake()->image('imagen.png')->size(1024),
        ];

        $datos = [
            'nombre' => 'Lugar con formatos válidos',
            'descripcion' => 'Descripción',
            'categoria_id' => $categoria->id,
            'imagenes' => $imagenes,
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertRedirect(route('lugares.index'));

        // Verificar que se guardaron las 3 imágenes
        $lugar = LugarTuristico::where('nombre', 'Lugar con formatos válidos')->first();
        $this->assertCount(3, $lugar->imagenes);
    }

    public function test_puede_crear_lugar_sin_imagenes()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $categoria = Categoria::factory()->create();

        $datos = [
            'nombre' => 'Lugar sin imágenes',
            'descripcion' => 'Descripción',
            'categoria_id' => $categoria->id,
            // Sin campo 'imagenes'
        ];

        $response = $this->actingAs($admin)
            ->post(route('lugares.store'), $datos);

        $response->assertRedirect(route('lugares.index'));

        // Verificar que el lugar se creó sin imágenes
        $lugar = LugarTuristico::where('nombre', 'Lugar sin imágenes')->first();
        $this->assertNotNull($lugar);
        $this->assertCount(0, $lugar->imagenes);
    }

    public function test_usuario_normal_no_puede_subir_imagenes()
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('usuario');

        $categoria = Categoria::factory()->create();

        $imagen = UploadedFile::fake()->image('lugar.jpg')->size(1024);

        $datos = [
            'nombre' => 'Intento de usuario',
            'descripcion' => 'Descripción',
            'categoria_id' => $categoria->id,
            'imagenes' => [$imagen],
        ];

        // Usuario normal intenta crear lugar (debe ser rechazado)
        $response = $this->actingAs($usuario)
            ->post(route('lugares.store'), $datos);

        // Debe redirigir o dar 403 (sin permiso)
        $this->assertContains($response->status(), [302, 403]);

        // Verificar que NO se creó el lugar
        $this->assertDatabaseMissing('lugar_turisticos', [
            'nombre' => 'Intento de usuario',
        ]);
    }
}
