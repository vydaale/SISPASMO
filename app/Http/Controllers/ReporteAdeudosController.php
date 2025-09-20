<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdeudosExport;
use App\Models\Diplomado;

class ReporteAdeudosController extends Controller
{
    public function mostrarReporte()
    {
        $diplomados = Diplomado::orderBy('nombre')->get();
        return view('administrador.reportes.adeudos.reporte', compact('diplomados'));
    }

    public function generarGraficaMes(Request $request)
    {
        $request->validate([
            'mes' => 'required|integer|between:1,12',
            'anio' => 'required|integer|digits:4',
        ]);

        $mes  = $request->input('mes');
        $anio = $request->input('anio');

        $concepto_adeudo = 'Colegiatura ' .
            ucfirst(Carbon::createFromDate($anio, $mes, 1)->locale('es')->monthName) .
            ' ' . $anio;

        $diplomados = Diplomado::withCount(['alumnos as alumnos_adeudos_count' => function ($query) use ($concepto_adeudo) {
            $query->whereDoesntHave('recibos', function ($q) use ($concepto_adeudo) {
                $q->where('concepto', $concepto_adeudo)
                  ->where('estatus', 'validado');
            });
        }])->get();

        $labels = $diplomados->pluck('nombre');
        $data   = $diplomados->pluck('alumnos_adeudos_count');

        return response()->json([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }


    public function generarGraficaAlumno(Request $request)
    {
        $request->validate([
            'mes'       => 'required|integer|between:1,12',
            'anio'      => 'required|integer|digits:4',
            'matricula' => 'required|string|max:255',
        ]);

        $mes       = $request->input('mes');
        $anio      = $request->input('anio');
        $matricula = $request->input('matricula');

        $concepto_adeudo = 'Colegiatura ' .
            ucfirst(Carbon::createFromDate($anio, $mes, 1)->locale('es')->monthName) .
            ' ' . $anio;

        $alumno = Alumno::where('matriculaA', $matricula)->first();

        $labels = [];
        $data   = [];

        if ($alumno) {
            $tienePago = $alumno->recibos()
                ->where('concepto', $concepto_adeudo)
                ->where('estatus', 'validado')
                ->exists();

            if ($tienePago) {
                $labels = ['Sin Adeudo'];
                $data   = [1];
            } else {
                $labels = ['Adeudo'];
                $data   = [1];
            }
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }

    public function exportarExcel(Request $request)
    {
        $request->validate([
            'mes'       => 'required|integer|between:1,12',
            'anio'      => 'required|integer|digits:4',
            'tipo'      => 'required|string|in:mes,alumno',
            'matricula' => 'nullable|string|max:255',
        ]);

        $mes       = $request->input('mes');
        $anio      = $request->input('anio');
        $tipo      = $request->input('tipo');
        $matricula = $request->input('matricula');

        $fileName = 'adeudos_' . $tipo . '_' . $anio . '_' . $mes . '.xlsx';
        $export = new AdeudosExport($mes, $anio, $matricula);
        
        return Excel::download($export, $fileName);
    }
}