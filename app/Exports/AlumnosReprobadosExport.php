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

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Alumno::whereHas('calificaciones', function ($query) {
            $query->where('id_modulo', $this->idModulo)->where('calificacion', '<', 80);
        })
        ->with(['diplomado', 'calificaciones' => function ($query) {
            $query->where('id_modulo', $this->idModulo);
        }])
        ->get();
    }

    /**
     * Define los encabezados para la hoja de cálculo.
     * @return array
     */
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

    /**
     * Mapea cada fila de la colección a una fila del archivo Excel.
     * @param mixed $row
     * @return array
     */
    public function map($alumno): array
    {
        $calificacion = $alumno->calificaciones->first();
        $modulo = Modulo::find($this->idModulo);
        
        return [
            $alumno->nombre_completo, // Asumiendo que tienes un accessor para nombre_completo o lo tienes como una columna. Si no, usa $alumno->nombre . ' ' . $alumno->apellidos.
            $alumno->matricula,
            $alumno->diplomado->nombre,
            $modulo->nombre,
            $calificacion->calificacion,
        ];
    }
}