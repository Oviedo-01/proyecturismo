<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->default(0)->after('capacidad');
            $table->foreignId('lugar_id')->nullable()->after('ubicacion')->constrained('lugar_turisticos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['precio', 'lugar_id']);
        });
    }
};