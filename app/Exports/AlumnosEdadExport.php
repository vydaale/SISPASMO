<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AlumnosEdadExport implements FromCollection, WithHeadings
{
    public function __construct(private string $dobColumn = 'usuarios.fecha_nac') {}

    public function headings(): array
    {
        return ['ID Alumno', 'Nombre', 'Apellido P', 'Apellido M', 'Edad', 'Matrícula', 'Grupo'];
    }

    public function collection()
    {
        $dob = $this->dobColumn;

        return DB::table('alumnos as a')
            ->join('usuarios as u', 'u.id_usuario', '=', 'a.id_usuario')
            ->whereNotNull(DB::raw($dob))
            ->selectRaw("
                a.id_alumno as id,
                u.nombre, u.apellidoP, u.apellidoM,
                TIMESTAMPDIFF(YEAR, $dob, CURDATE()) AS edad,
                a.matriculaA, a.grupo
            ")
            ->orderBy('edad')
            ->orderBy('apellidoP')
            ->orderBy('nombre')
            ->get();
    }
}