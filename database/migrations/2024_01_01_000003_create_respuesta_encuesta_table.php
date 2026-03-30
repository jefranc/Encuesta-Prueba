<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuesta_encuesta', function (Blueprint $table) {
            $table->integer('codigo_respuesta')->autoIncrement()->primary();
            $table->integer('codigo_encuesta')->nullable();
            $table->dateTime('fecha_respuesta')->nullable();

            $table->foreign('codigo_encuesta')
                ->references('codigo_encuesta')
                ->on('encuestas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuesta_encuesta');
    }
};
