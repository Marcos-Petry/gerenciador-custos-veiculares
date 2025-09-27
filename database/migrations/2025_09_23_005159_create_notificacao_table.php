<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacao', function (Blueprint $table) {
            $table->id('notcodigo');

            // Quem enviou e quem recebe
            $table->unsignedBigInteger('usuario_remetente_id');
            $table->unsignedBigInteger('usuario_destinatario_id');

            // Relacionamento com veículo/frota (nullable porque pode ser um ou outro)
            $table->unsignedBigInteger('veiculo_id')->nullable();
            $table->unsignedBigInteger('frota_id')->nullable();

            // Tipo da notificação: 1 = convite veículo, 2 = convite frota, 3 = alerta, etc.
            $table->unsignedTinyInteger('tipo');

            // Status: 0 = pendente, 1 = aceito, 2 = recusado
            $table->unsignedTinyInteger('status')->default(0);

            // Datas
            $table->timestamp('data_envio')->useCurrent();
            $table->timestamp('data_resposta')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('usuario_remetente_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('usuario_destinatario_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('veiculo_id')->references('veiculo_id')->on('veiculo')->onDelete('cascade');
            $table->foreign('frota_id')->references('frota_id')->on('frota')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacao');
    }
};
