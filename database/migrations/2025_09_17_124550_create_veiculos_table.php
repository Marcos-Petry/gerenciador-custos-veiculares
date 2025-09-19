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
        Schema::create('veiculo', function (Blueprint $table) {
            $table->id('veiculo_id');

            // Dono do veículo → FK para tabela users
            $table->foreignId('usuario_dono_id')
                ->constrained('users') // referencia users(id)
                ->onDelete('cascade');

            // Frota (opcional)
            $table->unsignedBigInteger('frota_id')->nullable();
            $table->foreign('frota_id')->references('frota_id')->on('frota')->onDelete('cascade');

            $table->string('modelo', 150);
            $table->string('placa', 10)->unique();
            $table->year('ano');
            $table->string('foto')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('veiculo');
    }
};
