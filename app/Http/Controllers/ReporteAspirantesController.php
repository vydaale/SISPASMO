<?php

namespace App\Http\Controllers;

use App\Models\Aspirante;
use App\Models\Diplomado;
use Illuminate\Http\Request;

class ReporteAspirantesController extends Controller
{
    const TIPO_BASICO = 'basico';
    const TIPO_INTERMEDIO_AVANZADO = 'intermedio y avanzado';

    public function mostrarReporte()
    {
        return view('administrador.reportes.aspirantesInteresados.reporte');
    }

    public function totalPorDiplomado(Request $request)
    {
        $tipoDiplomadoLargo = $request->input('tipo'); 

        if (empty($tipoDiplomadoLargo)) {
             return response()->json(['labels' => ['Selección inválida'], 'data' => [0]]);
        }
        
        $tipoDiplomadoEnum = $this->mapTipoToEnum($tipoDiplomadoLargo);
        $nombresDiplomados = Diplomado::where('tipo', $tipoDiplomadoEnum)->pluck('nombre');
        $total = Aspirante::whereIn('interes', $nombresDiplomados)->count();

        return response()->json([
            'labels' => [$tipoDiplomadoLargo],
            'data'   => [$total],
        ]);
    }

    public function comparacionTipos()
    {
        $tipos = [
            'Básico' => self::TIPO_BASICO,
            'Intermedio y Avanzado' => self::TIPO_INTERMEDIO_AVANZADO,
        ];

        $labels = array_keys($tipos);
        $data = [];
        
        foreach ($tipos as $label => $tipoEnum) {
            $nombresDiplomados = Diplomado::where('tipo', $tipoEnum)->pluck('nombre');
            
            $data[] = Aspirante::whereIn('interes', $nombresDiplomados)->count();
        }

        return response()->json([
            'labels' => $labels, 
            'data'   => $data,
        ]);
    }
    
    protected function mapTipoToEnum(string $tipoLargo): string
    {
        if (str_contains(strtolower($tipoLargo), 'básico')) {
            return self::TIPO_BASICO;
        }
        return self::TIPO_INTERMEDIO_AVANZADO;
    }
}