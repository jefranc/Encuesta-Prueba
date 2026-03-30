<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaPregunta extends Model
{
    protected $table = 'respuesta_pregunta';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['num_pregunta', 'codigo_encuesta', 'califica', 'codigo_respuesta'];

    public function pregunta()
    {
        return $this->belongsTo(PreguntaEncuesta::class, 'num_pregunta', 'num_pregunta');
    }

    public function respuestaEncuesta()
    {
        return $this->belongsTo(RespuestaEncuesta::class, 'codigo_respuesta', 'codigo_respuesta');
    }
}
