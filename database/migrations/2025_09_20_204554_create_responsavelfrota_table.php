<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responsavelfrota', function (Blueprint $table) {
            $table->id('refcodigo');
            $table->unsignedBigInteger('usucodigo');
            $table->unsignedBigInteger('frota_id');
            $table->timestamp('refdatacadastro')->useCurrent();
            $table->timestamps();

            // Foreign keys
            $table->foreign('usucodigo')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('frota_id')
                ->references('frota_id')
                ->on('frota')
                ->onDelete('cascade');

            // Restringe duplicados do mesmo usuário na mesma frota
            $table->unique(['usucodigo', 'frota_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsavelfrota');
    }
};
