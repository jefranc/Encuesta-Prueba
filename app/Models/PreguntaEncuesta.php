<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaEncuesta extends Model
{
    protected $table = 'preguntas_encuesta';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['num_pregunta', 'codigo_encuesta', 'descripcion'];

    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class, 'codigo_encuesta', 'codigo_encuesta');
    }

    public function respuestasPregunta()
    {
        return $this->hasMany(
            RespuestaPregunta::class,
            ['num_pregunta', 'codigo_encuesta'],
            ['num_pregunta', 'codigo_encuesta']
        );
    }
}
