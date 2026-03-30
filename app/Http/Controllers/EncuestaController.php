<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\RespuestaEncuesta;
use App\Models\RespuestaPregunta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EncuestaController extends Controller
{
    public function index()
    {
        $encuestas = Encuesta::orderBy('codigo_encuesta')->get();
        return view('encuestas.index', compact('encuestas'));
    }

    public function preguntas(int $codigo)
    {
        $encuesta = Encuesta::with('preguntas')->findOrFail($codigo);

        return response()->json([
            'encuesta' => $encuesta->nombre_encuesta,
            'preguntas' => $encuesta->preguntas->map(fn($p) => [
                'num_pregunta' => $p->num_pregunta,
                'descripcion'  => $p->descripcion,
            ]),
        ]);
    }

    public function guardar(Request $request)
    {
        $codigoEncuesta = (int) $request->input('codigo_encuesta');

        $encuesta = Encuesta::with('preguntas')->findOrFail($codigoEncuesta);
        $numPreguntas = $encuesta->preguntas->pluck('num_pregunta')->toArray();

        $rules = ['codigo_encuesta' => 'required|integer|exists:encuestas,codigo_encuesta'];
        foreach ($numPreguntas as $num) {
            $rules["respuestas.{$num}"] = 'required|integer|min:1|max:5';
        }

        $validated = $request->validate($rules, [
            'codigo_encuesta.required' => 'Debe seleccionar una encuesta.',
            'respuestas.*.required'    => 'Debe responder todas las preguntas.',
            'respuestas.*.min'         => 'La calificación mínima es 1.',
            'respuestas.*.max'         => 'La calificación máxima es 5.',
        ]);

        DB::transaction(function () use ($codigoEncuesta, $validated, $numPreguntas) {
            $respuestaEncuesta = RespuestaEncuesta::create([
                'codigo_encuesta' => $codigoEncuesta,
                'fecha_respuesta' => now(),
            ]);

            foreach ($numPreguntas as $num) {
                $valor = (int) $validated['respuestas'][$num];
                RespuestaPregunta::create([
                    'num_pregunta'    => $num,
                    'codigo_encuesta' => $codigoEncuesta,
                    'codigo_respuesta' => $respuestaEncuesta->codigo_respuesta,
                    'califica'        => $valor * 4,
                ]);
            }
        });

        return redirect('/')->with('success', '¡Encuesta enviada correctamente! Gracias por su participación.');
    }

    public function estadisticas()
    {
        $encuestas = Encuesta::with('preguntas')->get();

        $estadisticas = $encuestas->map(function (Encuesta $encuesta) {
            $nPreguntas   = $encuesta->preguntas->count();
            $maxPosible   = $nPreguntas * 5 * 4;

            $totalEncuestados = RespuestaEncuesta::where('codigo_encuesta', $encuesta->codigo_encuesta)->count();

            $sumaCalifica = RespuestaPregunta::where('codigo_encuesta', $encuesta->codigo_encuesta)->sum('califica');
            $totalRespuestas = RespuestaPregunta::where('codigo_encuesta', $encuesta->codigo_encuesta)->count();

            $promedio = $totalEncuestados > 0 ? $sumaCalifica / $totalEncuestados : 0;

            $porcentaje = $maxPosible > 0 ? round(($promedio / $maxPosible) * 100, 2) : 0;

            return [
                'codigo'           => $encuesta->codigo_encuesta,
                'nombre'           => $encuesta->nombre_encuesta,
                'n_preguntas'      => $nPreguntas,
                'max_posible'      => $maxPosible,
                'total_encuestados' => $totalEncuestados,
                'promedio'         => round($promedio, 2),
                'porcentaje'       => $porcentaje,
            ];
        });

        return view('encuestas.estadisticas', compact('estadisticas'));
    }
}
