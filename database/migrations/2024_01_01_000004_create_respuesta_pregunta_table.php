<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuesta_pregunta', function (Blueprint $table) {
            $table->integer('num_pregunta');
            $table->integer('codigo_encuesta');
            $table->integer('codigo_respuesta');
            $table->integer('califica')->nullable();

            $table->primary(['num_pregunta', 'codigo_encuesta', 'codigo_respuesta']);

            $table->foreign(['num_pregunta', 'codigo_encuesta'])
                ->references(['num_pregunta', 'codigo_encuesta'])
                ->on('preguntas_encuesta');

            $table->foreign('codigo_respuesta')
                ->references('codigo_respuesta')
                ->on('respuesta_encuesta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuesta_pregunta');
    }
};
