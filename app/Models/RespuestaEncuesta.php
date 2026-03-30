<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaEncuesta extends Model
{
    protected $table = 'respuesta_encuesta';
    protected $primaryKey = 'codigo_respuesta';
    public $timestamps = false;

    protected $fillable = ['codigo_encuesta', 'fecha_respuesta'];

    protected $casts = [
        'fecha_respuesta' => 'datetime',
    ];

    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class, 'codigo_encuesta', 'codigo_encuesta');
    }

    public function respuestasPregunta()
    {
        return $this->hasMany(RespuestaPregunta::class, 'codigo_respuesta', 'codigo_respuesta');
    }
}
