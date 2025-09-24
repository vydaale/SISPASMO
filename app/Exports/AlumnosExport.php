<?php

namespace App\Exports;

use App\Models\Alumno;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AlumnosExport implements FromCollection, WithHeadings
{
    protected $alumnos;

    public function __construct(Collection $alumnos)
    {
        $this->alumnos = $alumnos;
    }

    public function collection()
    {
        return $this->alumnos->map(function ($alumno) {
            return [
                'Nombre' => $alumno->nombre,
                'Apellido Paterno' => $alumno->apellidoP,
                'Apellido Materno' => $alumno->apellidoM,
                'Matricula' => $alumno->matricula, // Asegúrate de que tu modelo Alumno tenga el campo 'matricula'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Apellido Paterno',
            'Apellido Materno',
            'Matrícula',
        ];
    }
}