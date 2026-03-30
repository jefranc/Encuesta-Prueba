<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preguntas_encuesta', function (Blueprint $table) {
            $table->integer('num_pregunta');
            $table->integer('codigo_encuesta');
            $table->text('descripcion')->nullable();

            $table->primary(['num_pregunta', 'codigo_encuesta']);

            $table->foreign('codigo_encuesta')
                ->references('codigo_encuesta')
                ->on('encuestas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preguntas_encuesta');
    }
};
