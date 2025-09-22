<?php

namespace App\Http\Controllers;

use App\Models\Diplomado;
use App\Models\Alumno;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlumnosReprobadosExport;

class ReporteAlumnosReprobadosController extends Controller
{
    /**
     * Muestra la vista del reporte con todos los diplomados registrados.
     */
    public function mostrarReporte()
    {
        // Se obtienen todos los diplomados, sin filtrar por 'estatus'.
        $diplomados = Diplomado::orderBy('nombre')->get();
        return view('administrador.reportes.alumnosReprobados.reporte', compact('diplomados'));
    }

    /**
     * Devuelve los módulos de un diplomado seleccionado.
     */
    public function cargarModulos(Request $request)
    {
        $modulos = Modulo::where('id_diplomado', $request->input('id_diplomado'))->get();
        return response()->json($modulos);
    }

    /**
     * Gráfica 1: Total de alumnos reprobados.
     */
    public function totalReprobados(Request $request)
    {
        $idDiplomado = $request->input('id_diplomado');
        $idModulo    = $request->input('id_modulo');

        $reprobados = Alumno::whereHas('calificaciones', function ($query) use ($idModulo) {
            $query->where('id_modulo', $idModulo)->where('calificacion', '<', 80);
        })->count();

        $diplomado = Diplomado::find($idDiplomado);
        $modulo = Modulo::find($idModulo);

        return response()->json([
            'labels' => [$diplomado->nombre . ' - ' . $modulo->nombre],
            'data'   => [$reprobados],
        ]);
    }

    /**
     * Gráfica 2: Alumnos reprobados por rango de calificación.
     */
    public function calificacionesReprobados(Request $request)
    {
        $idModulo = $request->input('id_modulo');

        $reprobadosBajos = Alumno::whereHas('calificaciones', function ($query) use ($idModulo) {
            $query->where('id_modulo', $idModulo)->whereBetween('calificacion', [10, 59]);
        })->count();

        $reprobadosAltos = Alumno::whereHas('calificaciones', function ($query) use ($idModulo) {
            $query->where('id_modulo', $idModulo)->whereBetween('calificacion', [60, 79]);
        })->count();

        return response()->json([
            'labels' => ['10-59', '60-79'],
            'data'   => [$reprobadosBajos, $reprobadosAltos],
        ]);
    }

    /**
     * Exporta los datos a un archivo de Excel usando Maatwebsite.
     */
    public function exportarExcel(Request $request)
    {
        $idModulo = $request->input('id_modulo');

        if (!$idModulo) {
            return back()->with('error', 'Por favor, selecciona un módulo para exportar.');
        }

        // Se usa la fachada Excel para descargar el archivo
        return Excel::download(new AlumnosReprobadosExport($idModulo), 'alumnos_reprobados.xlsx');
    }
}