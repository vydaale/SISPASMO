<?php

namespace App\Http\Controllers;

use App\Models\Diplomado;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteAlumnosInscritosController extends Controller
{
    public function mostrarReporte()
    {
        $diplomados = Diplomado::orderBy('nombre')->get();
        
        return view('administrador.reportes.alumnosInscritos.reporte', compact('diplomados'));
    }

    public function alumnosTotales(Request $request)
    {
        $diplomadosIds = $request->input('diplomados', []);

        if (empty($diplomadosIds)) {
            $diplomados = Diplomado::all(); 
        } else {
            $diplomados = Diplomado::whereIn('id_diplomado', $diplomadosIds)->get();
        }

        $diplomados->loadCount('alumnos');

        $labels = $diplomados->pluck('nombre');
        $data   = $diplomados->pluck('alumnos_count');

        return response()->json([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }

    public function estatusAlumnos(Request $request)
    {
        $diplomadosIds = $request->input('diplomados', []);

        if (empty($diplomadosIds)) {
            $diplomados = Diplomado::all();
        } else {
            $diplomados = Diplomado::whereIn('id_diplomado', $diplomadosIds)->get();
        }

        $labels = $diplomados->pluck('nombre');

        $dataActivos = [];
        $dataBajas   = [];

        foreach ($diplomados as $diplomado) {
            $alumnosActivos = $diplomado->alumnos()->where('estatus', 'activo')->count();
            $alumnosBajas   = $diplomado->alumnos()->where('estatus', 'baja')->count();

            $dataActivos[] = $alumnosActivos;
            $dataBajas[]   = $alumnosBajas;
        }

        return response()->json([
            'labels' => $labels,
            'dataActivos' => $dataActivos,
            'dataBajas'   => $dataBajas,
        ]);
    }

    public function generarPdf(Request $request)
    {
        $imageData = $request->input('chart_data_url');
        $titulo = $request->input('titulo', 'Reporte');
        $subtitulo = $request->input('subtitulo', '');

        $data = [
            'titulo' => $titulo,
            'subtitulo' => $subtitulo,
            'imageData' => $imageData
        ];

        $pdf = Pdf::loadView('administrador.reportes.alumnosInscritos.pdf', $data);
        return $pdf->download('reporte_alumnos.pdf');
    }
}