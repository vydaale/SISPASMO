<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Models\Modulo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlumnosReprobadosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $idModulo;

    public function __construct(int $idModulo)
    {
        $this->idModulo = $idModulo;
    }

    public function collection()
    {
        return Alumno::whereHas('calificaciones', function ($query) {
            $query->where('id_modulo', $this->idModulo)->where('calificacion', '<', 80);
        })
        ->with(['usuario', 'diplomado', 'calificaciones' => function ($query) {
            $query->where('id_modulo', $this->idModulo);
        }])
        ->get();
    }

    public function headings(): array
    {
        return [
            'Nombre Completo',
            'Matrícula',
            'Diplomado',
            'Módulo',
            'Calificación'
        ];
    }

    public function map($alumno): array
    {
        // Se asegura de que el módulo y la calificación se obtengan correctamente
        $calificacion = $alumno->calificaciones->first();
        $modulo = Modulo::find($this->idModulo);
        
        // Retorna los datos en el orden correcto para las columnas
        return [
            $alumno->usuario->nombre . ' ' . $alumno->usuario->apellidoP . ' ' . $alumno->usuario->apellidoM,
            $alumno->matriculaA,
            $alumno->diplomado->nombre,
            $modulo->nombre,
            $calificacion ? $calificacion->calificacion : 'N/A',
        ];
    }
}