<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Aquí llamas a tus seeders
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}
