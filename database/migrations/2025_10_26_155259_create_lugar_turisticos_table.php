<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lugar_turisticos', function (Blueprint $table) {
            $table->id(); // ← ID estándar de Laravel

            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('direccion')->nullable();
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->string('horarios')->nullable();
            $table->string('precio')->nullable();
            $table->string('contacto')->nullable();

            // Campo que faltaba en tu BD de pruebas
            $table->decimal('promedio_calificacion', 3, 2)->default(0);

            // Foreign keys con convenciones correctas
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('creador_id')->constrained('users')->onDelete('cascade');

            $table->timestamp('fecha_creacion')->useCurrent();
            $table->boolean('visible')->default(true);

            // ← Esto te faltaba
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lugar_turisticos');
    }
};

