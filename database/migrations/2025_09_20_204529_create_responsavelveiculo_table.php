<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responsavelveiculo', function (Blueprint $table) {
            $table->id('revcodigo');
            $table->unsignedBigInteger('usucodigo');
            $table->unsignedBigInteger('veiculo_id');
            $table->timestamp('revdatacadastro')->useCurrent();
            $table->timestamps();

            // FK para tabela users
            $table->foreign('usucodigo')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // FK para tabela veiculo
            $table->foreign('veiculo_id')
                ->references('veiculo_id')
                ->on('veiculo')
                ->onDelete('cascade');

            // Restringe duplicados do mesmo usuário no mesmo veículo
            $table->unique(['usucodigo', 'veiculo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsavelveiculo');
    }
};
