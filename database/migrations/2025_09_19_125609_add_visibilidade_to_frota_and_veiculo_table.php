<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('frota', function (Blueprint $table) {
            $table->unsignedTinyInteger('visibilidade')
                ->default(0)   // 0 = privado, 1 = público
                ->after('descricao');
        });

        Schema::table('veiculo', function (Blueprint $table) {
            $table->unsignedTinyInteger('visibilidade')
                ->default(0)   // 0 = privado, 1 = público
                ->after('ano');
        });
    }

    public function down(): void
    {
        Schema::table('frota', function (Blueprint $table) {
            $table->dropColumn('visibilidade');
        });

        Schema::table('veiculo', function (Blueprint $table) {
            $table->dropColumn('visibilidade');
        });
    }
};