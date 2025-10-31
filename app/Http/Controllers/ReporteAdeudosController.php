<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdeudosExport;
use App\Models\Diplomado;
use Illuminate\Validation\Rule; // Se añade la clase Rule para required_if

class ReporteAdeudosController extends Controller
{
    /*
     * Muestra la vista principal del reporte de adeudos. Carga todos los diplomados disponibles para que 
        el administrador pueda filtrar.
    */
    public function mostrarReporte()
    {
        $diplomados = Diplomado::orderBy('nombre')->get();
        return view('administrador.reportes.adeudos.reporte', compact('diplomados'));
    }


    /*
     * Genera datos JSON para una gráfica de adeudos por diplomado para un mes y año específicos.
    */
    public function generarGraficaMes(Request $request)
    {
        $request->validate([
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|digits:4',
        ]);

        $mes  = $request->input('mes');
        $anio = $request->input('anio');

        /* Construye el concepto de la colegiatura (`Colegiatura Mes Año`). */
        $concepto_adeudo = 'Colegiatura ' .
            ucfirst(Carbon::createFromDate($anio, $mes, 1)->locale('es')->monthName) .
            ' ' . $anio;

        /* Consulta cuántos alumnos en cada diplomado no tienen un recibo 'validado'
            con ese concepto (alumnos_adeudos_count).*/
        $diplomados = Diplomado::withCount(['alumnos as alumnos_adeudos_count' => function ($query) use ($concepto_adeudo) {
            $query->whereDoesntHave('recibos', function ($q) use ($concepto_adeudo) {
                $q->where('concepto', $concepto_adeudo)
                  ->where('estatus', 'validado');
            });
        }])->get();

        $labels = $diplomados->pluck('nombre');
        $data   = $diplomados->pluck('alumnos_adeudos_count');

        /* Devuelve los nombres de los diplomados (labels) y los conteos (data). */
        return response()->json([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }


    /*
     * Genera datos JSON para una gráfica de estado de adeudos general (total de recibos pagados vs pendientes)
     * para un Alumno específico, sin depender de mes/año.
    */
    public function generarGraficaAlumno(Request $request)
    {
        /* ... (Validación omitida por brevedad) ... */
    
        $matricula = $request->input('matricula');
        
        /* 💥 SOLUCIÓN 1: Cargar la relación 'diplomado' */
        $alumno = Alumno::where('matriculaA', $matricula)->with('diplomado')->first();
    
        $labels = [];
        $data   = [];
    
        if ($alumno) {
            /* 💥 SOLUCIÓN 2: Proteger el acceso a la fecha de inicio con Null Coalescing y Nullsafe */
            $fechaInicio = Carbon::parse($alumno->diplomado?->fecha_inicio ?? now());
            $conceptos_teoricos = [];
            $pagados_count = 0;
            $pendientes_count = 0;
            
            // 1. Inscripción
            $conceptos_teoricos[] = 'Inscripción ' . $fechaInicio->year;
            
            // 2. Colegiaturas (Máximo 12, hasta la fecha actual)
            $fecha = $fechaInicio->copy();
            $limiteColegiaturas = 12; 
        
            for ($i = 0; $i < $limiteColegiaturas; $i++) {
                if ($fecha->greaterThan(now())) {
                    break;
                }
                // Asegúrate que Carbon::locale('es') sea válido si lo usas
                $conceptos_teoricos[] = 'Colegiatura ' . ucfirst($fecha->locale('es')->monthName) . ' ' . $fecha->year;
                $fecha->addMonth();
            }
            
            $recibos = $alumno->recibos; // Asegúrate que $alumno tiene la relación 'recibos' si no la cargas explícitamente
    
            foreach ($conceptos_teoricos as $concepto) {
                $recibo = $recibos->firstWhere('concepto', $concepto);
            
                if ($recibo && $recibo->estatus === 'validado') {
                    $pagados_count++;
                } else {
                    $pendientes_count++;
                }
            }
    
            if ($pagados_count === 0 && $pendientes_count === 0) {
                 $labels = ['Sin Adeudos Registrados'];
                 $data = [0]; 
            } else {
                 $labels = ['Pagados', 'Pendientes'];
                 $data   = [$pagados_count, $pendientes_count];
            }
        } else {
             $labels = ['Matrícula no encontrada'];
             $data = [0];
        }
    
        /* Devuelve el estado general del alumno. */
        return response()->json([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }

    /*
     * Exporta el reporte de adeudos a un archivo Excel (.xlsx).
     * Los parámetros de mes/año son requeridos solo si el tipo de reporte es 'mes'.
    */
    public function exportarExcel(Request $request)
    {
        $request->validate([
            'tipo'      => ['required', 'string', Rule::in(['mes', 'alumno'])],
            // Mes y Año son requeridos SOLO si el tipo es 'mes'.
            'mes'       => ['nullable', 'integer', 'between:1,12', Rule::requiredIf($request->input('tipo') === 'mes')],
            'anio'      => ['nullable', 'integer', 'digits:4', Rule::requiredIf($request->input('tipo') === 'mes')],
            // Matrícula es requerida SOLO si el tipo es 'alumno'.
            'matricula' => ['nullable', 'string', 'max:255', Rule::requiredIf($request->input('tipo') === 'alumno')],
        ]);

        $mes       = $request->input('mes');
        $anio      = $request->input('anio');
        $tipo      = $request->input('tipo');
        $matricula = $request->input('matricula');
        
        /* Se ajusta el nombre del archivo para el reporte general de alumno. */
        if ($tipo === 'alumno') {
            $fileName = 'adeudos_alumno_' . $matricula . '_general.xlsx';
        } else {
            // Caso por mes (mes y anio no serán nulos aquí debido a la validación)
            $fileName = 'adeudos_' . $tipo . '_' . $anio . '_' . $mes . '.xlsx';
        }

        /* Utiliza la clase `AdeudosExport` (de Maatwebsite/Excel) para generar el archivo, 
            basado en el tipo de reporte (`mes` o `alumno`) y los parámetros de fecha/matrícula.
            Se pasa null para mes/anio si es reporte general.
        */
        $export = new AdeudosExport($tipo, $mes, $anio, $matricula);        
        return Excel::download($export, $fileName);
    }
}