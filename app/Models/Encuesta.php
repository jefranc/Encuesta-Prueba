<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = 'encuestas';
    protected $primaryKey = 'codigo_encuesta';
    public $timestamps = false;

    protected $fillable = ['codigo_encuesta', 'nombre_encuesta'];

    public function preguntas()
    {
        return $this->hasMany(PreguntaEncuesta::class, 'codigo_encuesta', 'codigo_encuesta');
    }

    public function respuestas()
    {
        return $this->hasMany(RespuestaEncuesta::class, 'codigo_encuesta', 'codigo_encuesta');
    }
}
