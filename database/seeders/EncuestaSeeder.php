<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EncuestaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('encuestas')->insert([
            ['codigo_encuesta' => 1, 'nombre_encuesta' => 'ATENCION VIAL'],
            ['codigo_encuesta' => 2, 'nombre_encuesta' => 'ATENCION ODONTOLOGICA'],
            ['codigo_encuesta' => 3, 'nombre_encuesta' => 'ATENCION MEDICA'],
            ['codigo_encuesta' => 4, 'nombre_encuesta' => 'ATENCION LEGAL'],
            ['codigo_encuesta' => 5, 'nombre_encuesta' => 'ATENCION HOGAR'],
            ['codigo_encuesta' => 6, 'nombre_encuesta' => 'ATENCION EXTRANJERO'],
        ]);

        $preguntas = [
            [1, 'COMO CALIFICA LA ATENCION DEL AGENTE?'],
            [2, 'COMO CALIFICA LA ATENCION DEL PROVEEDOR?'],
            [3, 'COMO CALIFICA LA EFICACIA DEL SERVICIO?'],
            [4, 'COMO CALIFICA LA TIEMPO DE TERMINACION DEL SERVICIO?'],
            [5, 'EL COSTO COBRADO LE PARECIO EL ADECUADO?'],
        ];

        $rows = [];
        foreach (range(1, 6) as $codigoEncuesta) {
            foreach ($preguntas as [$numPregunta, $descripcion]) {
                $rows[] = [
                    'num_pregunta'    => $numPregunta,
                    'codigo_encuesta' => $codigoEncuesta,
                    'descripcion'     => $descripcion,
                ];
            }
        }

        // Pregunta extra solo para encuesta 1
        $rows[] = [
            'num_pregunta'    => 6,
            'codigo_encuesta' => 1,
            'descripcion'     => 'NNNNNNN?',
        ];

        DB::table('preguntas_encuesta')->insert($rows);
    }
}
