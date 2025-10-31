<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Models\Diplomado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AlumnosReprobadosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $idDiplomado;
    protected $tipo; // <-- nuevo atributo ("total" o "calificaciones")

    /**
     * @param int $idDiplomado
     * @param string $tipo Tipo de reporte ("total" o "calificaciones")
     */
    public function __construct(int $idDiplomado, string $tipo = 'total')
    {
        $this->idDiplomado = $idDiplomado;
        $this->tipo = $tipo;
    }

    public function collection()
    {
        $diplomado = Diplomado::find($this->idDiplomado);

        if (!$diplomado) {
            return collect();
        }

        $modulosIds = $diplomado->horarios->pluck('id_modulo')->unique();

        return Alumno::whereHas('calificaciones', function ($query) use ($modulosIds) {
            $query->whereIn('id_modulo', $modulosIds)->where('calificacion', '<', 80);
        })
        ->with(['usuario', 'diplomado', 'calificaciones' => function ($query) use ($modulosIds) {
            $query->whereIn('id_modulo', $modulosIds)->where('calificacion', '<', 80);
        }, 'calificaciones.modulo'])
        ->get()
        ->flatMap(function ($alumno) {
            return $alumno->calificaciones->map(function ($calif) use ($alumno) {
                return (object)[
                    'alumno' => $alumno,
                    'calificacion' => $calif
                ];
            });
        });
    }

    public function headings(): array
    {
        // Cabeceras base
        $headings = [
            'Nombre Completo',
            'Matrícula',
            'Diplomado',
            'Módulo Reprobado',
            'Calificación Obtenida'
        ];

        // Si es el reporte de comparación, agregamos la nueva columna
        if ($this->tipo === 'calificaciones') {
            $headings[] = 'Puede aprobar con 2 puntos de práctica';
        }

        return $headings;
    }

    public function map($item): array
    {
        $alumno = $item->alumno;
        $calificacion = $item->calificacion;
        $moduloNombre = $calificacion->modulo ? $calificacion->modulo->nombre_modulo : 'N/A';

        $fila = [
            $alumno->usuario->nombre . ' ' . $alumno->usuario->apellidoP . ' ' . $alumno->usuario->apellidoM,
            $alumno->matriculaA,
            $alumno->diplomado->nombre,
            $moduloNombre,
            $calificacion->calificacion,
        ];

        // Si es el reporte de comparación, añadimos la evaluación “Sí” o “No”
        if ($this->tipo === 'calificaciones') {
            $puedeAprobar = ($calificacion->calificacion >= 60 && $calificacion->calificacion <= 79)
                ? 'Sí'
                : 'No';
            $fila[] = $puedeAprobar;
        }

        return $fila;
    }
}