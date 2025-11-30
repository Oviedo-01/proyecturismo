<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LugarTuristicoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'descripcion' => $this->faker->paragraph(),
            'direccion' => $this->faker->address(),
            'latitud' => $this->faker->latitude(),
            'longitud' => $this->faker->longitude(),
            'horarios' => 'Lun-Dom 8am-6pm',
            'precio' => $this->faker->numberBetween(0, 100000),
            'contacto' => $this->faker->phoneNumber(),
            'visible' => true,
            'promedio_calificacion' => 0,
            'categoria_id' => Categoria::factory(),
            'creador_id' => User::factory(),
        ];
    }
}

