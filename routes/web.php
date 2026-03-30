<?php

use App\Http\Controllers\EncuestaController;
use Illuminate\Support\Facades\Route;

// Formulario principal de encuesta
Route::get('/', [EncuestaController::class, 'index'])->name('encuestas.index');

// AJAX: obtener preguntas de una encuesta
Route::get('/preguntas/{codigo}', [EncuestaController::class, 'preguntas'])->name('encuestas.preguntas');

// Guardar respuestas
Route::post('/guardar', [EncuestaController::class, 'guardar'])->name('encuestas.guardar');

// Vista de estadísticas
Route::get('/estadisticas', [EncuestaController::class, 'estadisticas'])->name('encuestas.estadisticas');
