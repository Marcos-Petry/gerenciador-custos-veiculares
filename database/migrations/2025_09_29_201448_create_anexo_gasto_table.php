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
        Schema::create('anexo_gasto', function (Blueprint $table) {
            $table->id('anexo_id');
            $table->unsignedBigInteger('gasto_id');
            $table->string('caminho'); // arquivo armazenado
            $table->timestamps();

            $table->foreign('gasto_id')->references('gasto_id')->on('gasto')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anexo_gasto');
    }
};
