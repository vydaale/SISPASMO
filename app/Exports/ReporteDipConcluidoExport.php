<?php

namespace App\Exports;

use App\Models\Diplomado;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping; // Usaremos WithMapping

// Implementamos WithMapping en lugar de hacer el map en collection()
class ReporteDipConcluidoExport implements FromCollection, WithHeadings, WithMapping
{
    protected $year;
    protected $reportType;

    public function __construct(int $year, string $reportType)
    {
        $this->year = $year;
        $this->reportType = $reportType;
    }

    /**
     * Define los encabezados de las columnas del archivo Excel.
     */
    public function headings(): array
    {
        // Los encabezados ahora reflejan los datos del diplomado más los datos del alumno.
        return [
            'ID Diplomado',
            'Diplomado',
            'Grupo',
            'Matrícula',
            'Nombre del Alumno',
            'Estatus', // Nuevo campo para Estatus
        ];
    }

    /**
     * Carga los datos, incluyendo la relación de alumnos filtrada, y aplana la colección.
     */
    public function collection()
    {
        // 1. Definir los estatus que queremos cargar
        $estatuses = [];
        if ($this->reportType === 'egresados') {
            $estatuses = ['egresado'];
        } elseif ($this->reportType === 'estatus') {
            $estatuses = ['activo', 'egresado'];
        } else {
            return collect();
        }

        // 2. Cargar todos los diplomados del año, incluyendo los alumnos con el estatus relevante.
        return Diplomado::whereYear('fecha_fin', $this->year)
            ->with(['alumnos' => function ($query) use ($estatuses) {
                // Pre-cargamos alumnos, limitando por estatus y cargando el usuario
                $query->whereIn('estatus', $estatuses)->with('usuario');
            }])
            ->get()
            // 3. Usamos flatMap para aplanar la estructura: cada alumno se convierte en una fila separada.
            ->flatMap(function ($diplomado) {
                // Si no hay alumnos, devolvemos un array vacío para no crear filas vacías (opcional)
                if ($diplomado->alumnos->isEmpty()) {
                    return collect();
                }

                // Creamos una nueva estructura para cada alumno
                return $diplomado->alumnos->map(function ($alumno) use ($diplomado) {
                    return (object) [
                        'diplomado_id' => $diplomado->id_diplomado,
                        'diplomado_nombre' => $diplomado->nombre,
                        'diplomado_grupo' => $diplomado->grupo,
                        'alumno_matricula' => $alumno->matriculaA,
                        'alumno_nombre' => trim(
                            optional($alumno->usuario)->nombre . ' ' .
                            optional($alumno->usuario)->apellidoP . ' ' .
                            optional($alumno->usuario)->apellidoM
                        ),
                        'alumno_estatus' => $alumno->estatus,
                    ];
                });
            });
    }

    /**
     * Mapea cada objeto aplanado a una fila del archivo Excel.
     */
    public function map($row): array
    {
        /* Mapeamos los datos de la estructura creada en flatMap a las columnas. */
        return [
            $row->diplomado_id,
            $row->diplomado_nombre,
            $row->diplomado_grupo,
            $row->alumno_matricula,
            $row->alumno_nombre,
            ucfirst($row->alumno_estatus),
        ];
    }
}