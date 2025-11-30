<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->enum('estado', ['confirmada', 'cancelada', 'pendiente'])->default('confirmada');
            $table->text('notas')->nullable(); // Notas adicionales del usuario
            $table->timestamp('fecha_reserva')->useCurrent();
            $table->timestamps();
            
            // Un usuario no puede reservar el mismo evento dos veces
            $table->unique(['user_id', 'evento_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};