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
                $query->where('id_modulo', $modulo->id_modulo)
                    ->where('calificacion', '<', 80.00);
            })
            ->withCount(['calificaciones' => function ($query) use ($modulo) {
                $query->where('id_modulo', $modulo->id_modulo)
                    ->where('calificacion', '<', 80.00);
            }])
            ->having('calificaciones_count', '>', 0)
            ->count();

            $reprobados = \DB::table('alumnos')
                ->whereIn('id_alumno', function($query) use ($modulo) {
                    $query->select('id_alumno')
                        ->from('calificaciones')
                        ->where('id_modulo', $modulo->id_modulo)
                        ->where('calificacion', '<', 80.00)
                        ->distinct(); 
                })
                ->count();
            
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

        $reprobadosBajos = \DB::table('calificaciones')
            ->whereIn('id_modulo', $modulosIds)
            ->whereBetween('calificacion', [0, 59])
            ->distinct('id_alumno')
            ->count('id_alumno');

        $reprobadosAltos = \DB::table('calificaciones')
            ->whereIn('id_modulo', $modulosIds)
            ->whereBetween('calificacion', [60, 79])
            ->distinct('id_alumno')
            ->count('id_alumno');

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