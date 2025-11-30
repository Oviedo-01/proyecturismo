<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Cargar los roles antes de correr cada test
        $this->seed(RolesSeeder::class);
    }

    /** @test */
    public function usuario_puede_iniciar_sesion_correctamente()
    {
        // Crear un usuario de prueba
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        // Intentar login
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        // Assert: Redirección exitosa (dashboard u otra ruta)
        $response->assertStatus(302);
        $this->assertAuthenticatedAs($user);
    }
}
