<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use App\Exports\PagosExport; // Importa la clase de exportación
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel; // Importa el facade de Excel

class ReportePagosSemMenController extends Controller
{
    /**
     * Muestra la vista del reporte con los filtros y la gráfica.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function mostrarReporte(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $periodo = $request->input('periodo');

        $pagos = collect();

        if ($fechaInicio && $fechaFin) {
            $query = Recibo::with(['alumno', 'alumno.diplomado'])
                ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                ->where('estatus', 'validado')
                ->orderBy('fecha_pago');

            if ($periodo === 'semanal') {
                $pagos = $query->get()->groupBy(function($item) {
                    return Carbon::parse($item->fecha_pago)->startOfWeek()->format('Y-m-d');
                })->map(function($semana) {
                    return [
                        'fecha_pago' => $semana->first()->fecha_pago,
                        'monto' => $semana->sum('monto')
                    ];
                });
            } elseif ($periodo === 'mensual') {
                $pagos = $query->get()->groupBy(function($item) {
                    return Carbon::parse($item->fecha_pago)->format('Y-m');
                })->map(function($mes) {
                    return [
                        'fecha_pago' => $mes->first()->fecha_pago,
                        'monto' => $mes->sum('monto')
                    ];
                });
            }
        }

        return view('administrador.reportes.pagosSemMen.pagos', compact('pagos'));
    }

    /**
     * Exporta los datos detallados a un archivo Excel.
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $fileName = 'reporte_pagos_' . $fechaInicio . '_a_' . $fechaFin . '.xlsx';

        // Llama a la clase de exportación para generar y descargar el archivo
        return Excel::download(new PagosExport($fechaInicio, $fechaFin), $fileName);
    }
}