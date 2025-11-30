<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear o actualizar el usuario admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@tuapp.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@tuapp.com',
                'password' => Hash::make('tu_password_seguro'),
                // 'telefono', 'avatar' puedes omitirlos si permiten NULL
            ]
        );

        // 2. Crear el rol 'admin' si no existe
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // 3. Asignar el rol al usuario
        $admin->assignRole($role);
    }
}
