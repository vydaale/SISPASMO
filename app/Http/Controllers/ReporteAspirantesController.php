<?php

namespace App\Http\Controllers;

use App\Models\Aspirante;
use Illuminate\Http\Request;

class ReporteAspirantesController extends Controller
{
    public function mostrarReporte()
    {
        return view('administrador.reportes.aspirantesInteresados.reporte');
    }

    public function totalPorDiplomado(Request $request)
    {
        $tipoDiplomado = $request->input('tipo');

        $total = Aspirante::where('interes', $tipoDiplomado)->count();

        return response()->json([
            'labels' => [$tipoDiplomado],
            'data'   => [$total],
        ]);
    }

    public function comparacionTipos()
    {
        $tipos = [
            'Diplomado nivel básico',
            'Diplomado intermedio y avanzado',
        ];

        $data = [];
        foreach ($tipos as $tipo) {
            $data[] = Aspirante::where('interes', $tipo)->count();
        }

        return response()->json([
            'labels' => $tipos,
            'data'   => $data,
        ]);
    }
}