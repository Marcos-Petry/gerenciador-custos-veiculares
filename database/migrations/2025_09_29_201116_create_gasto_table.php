<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gasto', function (Blueprint $table) {
            $table->id('gasto_id');

            // FK para veículo
            $table->unsignedBigInteger('veiculo_id');
            $table->foreign('veiculo_id')
                ->references('veiculo_id')
                ->on('veiculo')
                ->onDelete('cascade');

            // FK para usuário
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Categoria do gasto (enum inteiro)
            $table->unsignedTinyInteger('categoria');
            // 1=Combustível, 2=Manutenção, 3=Seguro, 4=Imposto, 5=Outro

            // Dados do gasto
            $table->string('descricao', 255)->nullable();
            $table->decimal('valor', 10, 2);
            $table->date('data_gasto');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto');
    }
};
