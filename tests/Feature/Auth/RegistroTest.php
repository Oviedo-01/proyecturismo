<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolesSeeder;

class RegistroTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Importante: Cargar los roles para que Spatie no lance error
        $this->seed(RolesSeeder::class);
    }

    public function test_usuario_puede_registrarse_exitosamente()
    {
        $response = $this->post('/register', [
            'name' => 'Juan Perez',
            'email' => 'juan@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'email' => 'juan@example.com'
        ]);
    }
}
