<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlumnosEdadExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ReporteAlumnosEdadController extends Controller
{
    private string $dobColumn = 'u.fecha_nac';

    public function index()
    {
        return view('administrador.reportes.alumnosEdad.index');
    }

    public function chartData()
{
    $dob = $this->dobColumn;

    $row = \DB::table('alumnos as a')
        ->join('usuarios as u', 'u.id_usuario', '=', 'a.id_usuario')
        ->whereNotNull($dob)
        ->selectRaw("
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) BETWEEN 17 AND 21 THEN 1 ELSE 0 END) AS r_17_21,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) BETWEEN 22 AND 26 THEN 1 ELSE 0 END) AS r_22_26,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) BETWEEN 27 AND 31 THEN 1 ELSE 0 END) AS r_27_31,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) BETWEEN 32 AND 35 THEN 1 ELSE 0 END) AS r_31_35,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) BETWEEN 36 AND 40 THEN 1 ELSE 0 END) AS r_36_40,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) BETWEEN 41 AND 45 THEN 1 ELSE 0 END) AS r_41_45,
            SUM(CASE WHEN TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) >= 50 THEN 1 ELSE 0 END) AS r_50p
        ")
        ->first();

    return response()->json([
        'labels' => ['17-21','22-26','27-31','31-35','36-40','41-45','50+'],
        'data'   => [
            (int)($row->r_17_21 ?? 0),
            (int)($row->r_22_26 ?? 0),
            (int)($row->r_27_31 ?? 0),
            (int)($row->r_31_35 ?? 0),
            (int)($row->r_36_40 ?? 0),
            (int)($row->r_41_45 ?? 0),
            (int)($row->r_50p   ?? 0),
        ],
    ]);
}

public function table()
{
    $dob = $this->dobColumn;

    $alumnos = \DB::table('alumnos as a')
        ->join('usuarios as u', 'u.id_usuario', '=', 'a.id_usuario')
        ->whereNotNull($dob)
        ->selectRaw("
            a.id_alumno,
            u.nombre, u.apellidoP, u.apellidoM,
            TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) AS edad,
            a.matriculaA, a.grupo
        ")
        ->orderBy('edad')
        ->orderBy('apellidoP')
        ->orderBy('nombre')
        ->paginate(20);

    return view('administrador.reportes.alumnosEdad.tabla', compact('alumnos'));
}

public function excel()
{
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\AlumnosEdadExport($this->dobColumn),
        'alumnos_por_edad.xlsx'
    );
}

public function pdf()
{
    $dob = $this->dobColumn;

    $alumnos = \DB::table('alumnos as a')
        ->join('usuarios as u', 'u.id_usuario', '=', 'a.id_usuario')
        ->whereNotNull($dob)
        ->selectRaw("
            a.id_alumno,
            u.nombre, u.apellidoP, u.apellidoM,
            TIMESTAMPDIFF(YEAR, {$dob}, CURDATE()) AS edad,
            a.matriculaA, a.grupo
        ")
        ->orderBy('edad')
        ->orderBy('apellidoP')
        ->orderBy('nombre')
        ->get();

    $chartDataResponse = $this->chartData();
    $chartData = json_decode($chartDataResponse->getContent(), true);

    $config = [
        'type' => 'bar',
        'data' => [
            'labels' => $chartData['labels'],
            'datasets' => [
                [
                    'label' => 'Total de Alumnos',
                    'data' => $chartData['data'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ]
            ]
        ],
        'options' => [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Alumnos por Rango de Edad',
                ]
            ]
        ]
    ];

    $chartUrl = 'https://quickchart.io/chart?width=500&height=300&c=' . urlencode(json_encode($config));

    $chart_image = Http::get($chartUrl)->body();
    $chart_data_url = 'data:image/png;base64,' . base64_encode($chart_image);
    
    $titulo = 'Reporte de Alumnos por Edad';
    $fecha = Carbon::now()->isoFormat('D MMMM YYYY');

    $pdf = PDF::loadView('administrador.reportes.alumnosEdad.pdf_grafica', compact('alumnos', 'chart_data_url', 'titulo', 'fecha'));

    return $pdf->download('alumnos_por_edad.pdf');
}

}
