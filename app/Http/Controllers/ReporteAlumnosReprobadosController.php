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

    public function totalReprobados(Request $request)
    {
        $idDiplomado = $request->input('id_diplomado');
        $diplomado   = Diplomado::find($idDiplomado);

        if (!$diplomado) {
            return response()->json(['labels' => ['Error'], 'data' => [0]], 404);
        }

        $modulosIds = $diplomado->horarios->pluck('id_modulo')->unique();

        $modulos = Modulo::whereIn('id_modulo', $modulosIds)->get();
        
        $labels = [];
        $data = [];

        foreach ($modulos as $modulo) {
            $reprobados = Alumno::whereHas('calificaciones', function ($query) use ($modulo) {
                $query->where('id_modulo', $modulo->id_modulo)->where('calificacion', '<', 80);
            })->count();

            $labels[] = $modulo->nombre_modulo; 
            $data[] = $reprobados;
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }

    public function calificacionesReprobados(Request $request)
    {
        $idDiplomado = $request->input('id_diplomado');
        $diplomado   = Diplomado::find($idDiplomado);

        if (!$diplomado) {
            return response()->json(['labels' => ['Error'], 'data' => [0, 0]], 404);
        }

        $modulosIds = $diplomado->horarios->pluck('id_modulo')->unique();

        $reprobadosBajos = Alumno::whereHas('calificaciones', function ($query) use ($modulosIds) {
            $query->whereIn('id_modulo', $modulosIds)->whereBetween('calificacion', [0, 59]);
        })->count();

        $reprobadosAltos = Alumno::whereHas('calificaciones', function ($query) use ($modulosIds) {
            $query->whereIn('id_modulo', $modulosIds)->whereBetween('calificacion', [60, 79]);
        })->count();

        return response()->json([
            'labels' => ['0-59', '60-79'],
            'data'   => [$reprobadosBajos, $reprobadosAltos],
        ]);
    }

    public function exportarExcel(Request $request)
    {
        $idDiplomado = $request->input('id_diplomado');

        if (!$idDiplomado) {
            return back()->with('error', 'Por favor, selecciona un diplomado para exportar.');
        }

        return Excel::download(new AlumnosReprobadosExport($idDiplomado, true), 'alumnos_reprobados_diplomado_' . $idDiplomado . '.xlsx');
    }
}