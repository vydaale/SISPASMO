<?php

namespace App\Http\Controllers;

use App\Models\Diplomado;
use App\Models\Alumno;
use App\Models\Modulo;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlumnosReprobadosExport;

class ReporteAlumnosReprobadosController extends Controller
{
    public function mostrarReporte()
    {
        $diplomados = Diplomado::orderBy('nombre')->get();
        return view('administrador.reportes.alumnosReprobados.reporte', compact('diplomados'));
    }

    public function cargarModulos(Request $request)
    {
        $idDiplomado = $request->input('id_diplomado');

        // Esta es la consulta corregida que filtra los módulos a través de la tabla de horarios
        $modulos = Modulo::whereHas('horarios', function ($query) use ($idDiplomado) {
            $query->where('id_diplomado', $idDiplomado);
        })->get();
        
        return response()->json($modulos);
    }

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

    public function exportarExcel(Request $request)
    {
        $idModulo = $request->input('id_modulo');

        if (!$idModulo) {
            return back()->with('error', 'Por favor, selecciona un módulo para exportar.');
        }

        return Excel::download(new AlumnosReprobadosExport($idModulo), 'alumnos_reprobados.xlsx');
    }
}