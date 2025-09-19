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
        Schema::create('frota', function (Blueprint $table) {
            $table->id('frota_id');

            // Dono da frota → FK para users
            $table->foreignId('usuario_dono_id')
                ->constrained('users') // referencia users(id)
                ->onDelete('cascade');

            $table->string('nome', 150);
            $table->string('descricao', 300)->nullable();
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frota');
    }
};
