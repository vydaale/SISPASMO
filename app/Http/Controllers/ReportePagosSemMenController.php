<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use App\Exports\PagosExport; 
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportePagosSemMenController extends Controller
{
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

            $pagosEncontrados = $query->get();

            if ($pagosEncontrados->isNotEmpty()) {
                if ($periodo === 'semanal') {
                    $pagos = $pagosEncontrados->groupBy(function($item) {
                        return Carbon::parse($item->fecha_pago)->startOfWeek()->format('Y-m-d');
                    })->map(function($semana) {
                        return [
                            'fecha_pago' => $semana->first()->fecha_pago,
                            'monto' => $semana->sum('monto')
                        ];
                    });
                } elseif ($periodo === 'mensual') {
                    $pagos = $pagosEncontrados->groupBy(function($item) {
                        return Carbon::parse($item->fecha_pago)->format('Y-m');
                    })->map(function($mes) {
                        return [
                            'fecha_pago' => $mes->first()->fecha_pago,
                            'monto' => $mes->sum('monto')
                        ];
                    });
                }
            }
        }

        return view('administrador.reportes.pagosSemMen.pagos', compact('pagos'));
    }

    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $fileName = 'reporte_pagos_' . $fechaInicio . '_a_' . $fechaFin . '.xlsx';

        return Excel::download(new PagosExport($fechaInicio, $fechaFin), $fileName);
    }
}