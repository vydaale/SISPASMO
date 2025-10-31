<?php

namespace App\Exports;

use App\Models\Alumno;
use App\Models\Recibo; // Necesitamos el modelo Recibo
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

/*
 * Clase de exportación para generar listados de Adeudos.
 * La colección puede ser un listado de Alumnos (por mes) o un listado de Recibos Pendientes (por alumno).
 */
class AdeudosExport implements FromCollection, WithHeadings, WithMapping
{
    private $mes;
    private $anio;
    private $matricula;
    private $tipo;

    public function __construct($tipo, $mes = null, $anio = null, $matricula = null)
    {
        $this->tipo = $tipo;
        $this->mes = $mes;
        $this->anio = $anio;
        $this->matricula = $matricula;
    }

    /*
     * Define la colección de datos que se exportará, basada en el tipo de reporte.
     * Retorna una colección de Alumnos (Reporte por mes) o una colección de Recibos (Reporte por alumno).
    */
    public function collection()
    {
        if ($this->tipo === 'alumno' && !empty($this->matricula)) {

            $alumno = Alumno::where('matriculaA', $this->matricula)
                ->with(['recibos', 'diplomado', 'usuario'])
                ->first();
        
            if (!$alumno) {
                return collect([]);
            }
        
            $fechaInicio = Carbon::parse($alumno->diplomado->fecha_inicio ?? now());
            $meses = [];
        
            // 1. Agregamos inscripción (1 ítem)
            $meses[] = 'Inscripción ' . $fechaInicio->year;
        
            // 2. Generar las Colegiaturas: Límite de 12 meses (duración del diplomado).
            $fecha = $fechaInicio->copy();
            $limiteColegiaturas = 12; // Máximo de 12 colegiaturas
        
            for ($i = 0; $i < $limiteColegiaturas; $i++) {
                
                // Si la fecha de la colegiatura es posterior al día de hoy, 
                // significa que el adeudo no ha vencido, por lo que detenemos el conteo.
                if ($fecha->greaterThan(now())) {
                    break;
                }

                $meses[] = 'Colegiatura ' . ucfirst($fecha->locale('es')->monthName) . ' ' . $fecha->year;
                $fecha->addMonth(); // Avanzamos al siguiente mes
            }
        
            $recibos = $alumno->recibos;
            $adeudos = [];
        
            // 3. Verificación de adeudos vs recibos.
            foreach ($meses as $concepto) {
                $recibo = $recibos->firstWhere('concepto', $concepto);
        
                if (!$recibo || $recibo->estatus !== 'validado') {
                    // Aquí usamos la fecha del concepto para el monto (si se conoce)
                    $monto_estimado = 0; 
                    if ($recibo) {
                        $monto_estimado = $recibo->monto;
                    } 
                    // Nota: Si el recibo es null, el monto es 0, a menos que lo determines por otra lógica.

                    $adeudos[] = (object)[
                        'concepto' => $concepto,
                        'monto' => $monto_estimado,
                        'estatus' => $recibo->estatus ?? 'Pendiente',
                        'alumno' => $alumno
                    ];
                }
            }
        
            return collect($adeudos);
        }
        
        // Si el tipo no es 'alumno', el método debe devolver una colección de Alumnos, 
        // basada en la lógica de adeudos por mes (Reporte por Mes).
        // Tu método collection() solo tiene implementada la lógica de 'alumno'. 
        // DEBES añadir la lógica para el reporte de tipo 'mes' aquí.
        if ($this->tipo === 'mes' && !empty($this->mes) && !empty($this->anio)) {
            // Lógica de Reporte por Mes (ejemplo de cómo debería ser)
            $concepto_adeudo = 'Colegiatura ' .
                ucfirst(Carbon::createFromDate($this->anio, $this->mes, 1)->locale('es')->monthName) .
                ' ' . $this->anio;

            $alumnos = Alumno::whereDoesntHave('recibos', function ($q) use ($concepto_adeudo) {
                $q->where('concepto', $concepto_adeudo)
                ->where('estatus', 'validado');
            })->with(['diplomado', 'usuario'])->get();
            
            return $alumnos;
        }
        
        return collect([]);
    }


    /*
     * Define los encabezados de las columnas del archivo Excel.
    */
    public function headings(): array
    {
        if ($this->tipo === 'alumno') {
            return [
                'Matricula',
                'Nombre del alumno',
                'Concepto adeudado',
                'Monto adeudado', // Nueva columna para el monto
                'Estatus del recibo',
                'Diplomado',
            ];
        }

        return [
            'Matricula',
            'Nombre del alumno',
            'Concepto de adeudo',
            'Grupo',
            'Mes y año del adeudo',
        ];
    }


    /*
     * Mapea cada objeto de la colección a una fila del archivo Excel.
    */
    public function map($item): array
    {
        // Mapeo para Reporte por Matrícula (el item es un Recibo)
        if ($this->tipo === 'alumno') {
            $alumno = $item->alumno;
            $nombreCompleto = trim(
                ($alumno->usuario->nombre    ?? '') . ' ' .
                ($alumno->usuario->apellidoP ?? '') . ' ' .
                ($alumno->usuario->apellidoM ?? '')
            );
            
            return [
                $alumno->matriculaA ?? '—',
                $nombreCompleto ?: '—',
                $item->concepto,
                '$' . number_format($item->monto ?? 0, 2), // Asumiendo que el recibo tiene el campo 'monto'
                $item->estatus,
                $alumno->diplomado?->nombre ?? '—',
            ];
        }

        // Mapeo para Reporte por Mes (el item es un Alumno)
        // Si se llega aquí, se asume que mes y anio están definidos
        $mesNombre = Carbon::createFromDate($this->anio, $this->mes, 1)
            ->locale('es')
            ->monthName;

        $nombreCompleto = trim(
            ($item->usuario->nombre    ?? '') . ' ' .
            ($item->usuario->apellidoP ?? '') . ' ' .
            ($item->usuario->apellidoM ?? '')
        );

        $concepto_adeudo = 'Colegiatura ' . ucfirst($mesNombre) . ' ' . $this->anio;

        return [
            $item->matriculaA ?? '—',
            $nombreCompleto ?: '—',
            $concepto_adeudo,
            $item->diplomado?->grupo ?? '—',
            ucfirst($mesNombre) . ' ' . $this->anio,
        ];
    }
}