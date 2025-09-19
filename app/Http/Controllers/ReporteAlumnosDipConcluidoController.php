<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Diplomado;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\ReporteDipConcluidoExport;

class ReporteAlumnosDipConcluidoController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->query('year', date('Y'));

        $egresadosAnual = $this->getEgresadosPorAnio($year);
        $comparacionEstatus = $this->getComparacionEstatus($year);

        $years = Diplomado::selectRaw('YEAR(fecha_fin) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
        return view('administrador.reportes.alumnosDiplomado.diplomadoEgresados', compact('egresadosAnual', 'comparacionEstatus', 'year', 'years'));
    }

    public function downloadExcel(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $reportType = $request->query('report_type');

        if ($reportType === 'egresados') {
            return Excel::download(new ReporteDipConcluidoExport($year, 'egresados'), "egresadosAnual_$year.xlsx");
        }

        if ($reportType === 'estatus') {
            return Excel::download(new ReporteDipConcluidoExport($year, 'estatus'), "comparacionEstatus_$year.xlsx");
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }

    private function getEgresadosPorAnio($year)
    {
        return Diplomado::whereYear('fecha_fin', $year)
            ->withCount(['alumnos as egresados' => function ($query) {
                $query->where('estatus', 'egresado');
            }])
            ->get();
    }

    private function getComparacionEstatus($year)
    {
        return Diplomado::whereYear('fecha_fin', $year)
            ->withCount(['alumnos as activos' => function ($query) {
                $query->where('estatus', 'activo');
            }])
            ->withCount(['alumnos as egresados' => function ($query) {
                $query->where('estatus', 'egresado');
            }])
            ->get();
    }
}