<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lugar_turisticos', function (Blueprint $table) {
            $table->id('id_lugar');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('direccion')->nullable();
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->string('horarios')->nullable();
            $table->string('precio')->nullable();
            $table->string('contacto')->nullable();
            $table->foreignId('id_categoria')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('id_creador')->constrained('users')->onDelete('cascade');
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->boolean('visible')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lugar_turisticos');
    }
};
