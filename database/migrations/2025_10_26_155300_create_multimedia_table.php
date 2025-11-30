<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('multimedia', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['imagen', 'video']);
            $table->string('url');
            $table->string('formato')->nullable();
            $table->integer('tamano')->nullable();
            $table->text('descripcion')->nullable();

            $table->foreignId('lugar_id')->nullable()->constrained('lugar_turisticos')->onDelete('cascade');
            $table->foreignId('evento_id')->nullable()->constrained('eventos')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('multimedia');
    }
};
