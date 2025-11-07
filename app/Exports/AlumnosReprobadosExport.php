<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Models\Diplomado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


/*
 * Clase de exportación para generar un listado detallado de Alumnos que han reprobado en módulos de un Diplomado específico (calificación menor a 80).
    La colección se aplana para que cada fila represente una Calificación reprobatoria de un alumno en un módulo.
*/ 
class AlumnosReprobadosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $idDiplomado;
    protected $tipo;

    /*
     * Crea una nueva instancia de exportación.
    */
    public function __construct(int $idDiplomado, string $tipo = 'total')
    {
        $this->idDiplomado = $idDiplomado;
        $this->tipo = $tipo;
    }

    /*
     * Define la colección de datos que se exportará.
    */
    public function collection()
    {
        $diplomado = Diplomado::find($this->idDiplomado);

        if (!$diplomado) {
            return collect();
        }

        /* Obtiene los IDs de todos los módulos asociados a este diplomado. */
        $modulosIds = $diplomado->horarios->pluck('id_modulo')->unique();

        return Alumno::whereHas('calificaciones', function ($query) use ($modulosIds) {
            /* Carga solo las calificaciones reprobadas de ESTOS módulos para el mapeo. */
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

    /*
     * Define los encabezados de las columnas del archivo Excel.
    */
    public function headings(): array
    {
        $headings = [
            'Nombre Completo',
            'Matrícula',
            'Diplomado',
            'Módulo Reprobado',
            'Calificación Obtenida'
        ];

        /* Añade una columna condicional si el reporte es de tipo 'calificaciones' (comparación global). */
        if ($this->tipo === 'calificaciones') {
            $headings[] = 'Puede aprobar con 2 puntos de práctica';
        }

        return $headings;
    }

    /*
     * Mapea el objeto aplanado a una fila del archivo Excel.
    */
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

        if ($this->tipo === 'calificaciones') {
            $puedeAprobar = ($calificacion->calificacion >= 60 && $calificacion->calificacion <= 79)
                ? 'Sí'
                : 'No';
            $fila[] = $puedeAprobar;
        }

        return $fila;
    }
}