<?php

namespace App\Http\Controllers;

use App\Models\Aspirante;
use App\Models\Diplomado;
use Illuminate\Http\Request;
use App\Exports\AspirantesExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteAspirantesController extends Controller
{
    const TIPO_BASICO = 'basico';
    const TIPO_INTERMEDIO_AVANZADO = 'intermedio y avanzado';

    /*
     * Muestra la vista principal del reporte de Aspirantes interesados.
    */
    public function mostrarReporte()
    {
        return view('administrador.reportes.aspirantesInteresados.reporte');
    }

    /*
     * Genera el total de aspirantes interesados en un tipo de diplomado específico.
    */
    public function totalPorDiplomado(Request $request)
    {
        /* Convierte la cadena del tipo de diplomado (ej. 'Básico') a su valor de enumeración (`basico`). */
        $tipoDiplomadoLargo = $request->input('tipo'); 

        if (empty($tipoDiplomadoLargo)) {
             return response()->json(['labels' => ['Selección inválida'], 'data' => [0]]);
        }
        
        /* Usamos el método centralizado para obtener los nombres de diplomados */
        $nombresDiplomados = $this->getNombresDiplomados($tipoDiplomadoLargo); 

        /* Cuenta los aspirantes cuyo campo `interes` coincide con esos nombres. */
        $total = Aspirante::whereIn('interes', $nombresDiplomados)->count();

        return response()->json([
            'labels' => [$tipoDiplomadoLargo],
            'data'   => [$total],
        ]);
    }

    public function exportarExcel(Request $request)
    {
        $modo = $request->get('modo', 'total');
        $tipo = $request->get('tipo', 'todos');
        $nombresDiplomados = $this->getNombresDiplomados($tipo); 

        return Excel::download(new AspirantesExport($modo, $tipo, $nombresDiplomados), 'aspirantes_interesados.xlsx');
    }


    /*
     * Genera datos para una gráfica comparativa del total de aspirantes por tipo de diplomado.
    */
    public function comparacionTipos()
    {
        // En este caso, simplemente usamos 'todos' para que getNombresDiplomados
        // nos devuelva todos los diplomados de tipo básico e intermedio/avanzado.
        $nombresDiplomados = $this->getNombresDiplomados('todos'); 

        // Los datos para la gráfica de comparación deben ser específicos por tipo
        $tipos = [
            'Básico' => self::TIPO_BASICO,
            'Intermedio y Avanzado' => self::TIPO_INTERMEDIO_AVANZADO,
        ];

        $labels = array_keys($tipos);
        $data = [];
        
        foreach ($tipos as $label => $tipoEnum) {
            /* Obtenemos solo los nombres de diplomados de este tipo. */
            $nombresDiplomadosPorTipo = Diplomado::where('tipo', $tipoEnum)->pluck('nombre');
            
            /* Contamos solo a los interesados en ese tipo. */
            $data[] = Aspirante::whereIn('interes', $nombresDiplomadosPorTipo)->count();
        }

        return response()->json([
            'labels' => $labels, 
            'data'   => $data,
        ]);
    }
    

    /**
     * Obtiene los nombres de los diplomados basándose en un tipo o en todos los conocidos.
     * Este método centraliza la lógica de filtrado para garantizar la consistencia entre la gráfica y la exportación.
     * @param string $tipo 'basico', 'intermedio y avanzado', o 'todos'.
     * @return array
     */
    protected function getNombresDiplomados(string $tipo): array
    {
        $tiposConocidos = [self::TIPO_BASICO, self::TIPO_INTERMEDIO_AVANZADO];

        // Si es 'todos' o vacío, obtenemos los nombres de diplomados de todos los tipos conocidos
        if (str_contains(strtolower($tipo), 'todos') || $tipo === '') {
            return Diplomado::whereIn('tipo', $tiposConocidos)->pluck('nombre')->toArray();
        }
        
        // Si es un tipo específico, obtenemos solo los nombres de diplomados de ese tipo.
        $tipoEnum = $this->mapTipoToEnum($tipo);
        return Diplomado::where('tipo', $tipoEnum)->pluck('nombre')->toArray();
    }


    /*
     * Mapea el nombre largo del tipo de diplomado (ej. 'Básico') a su valor de enumeración
        utilizado en la base de datos (ej. 'basico').
    */
    protected function mapTipoToEnum(string $tipoLargo): string
    {
        if (str_contains(strtolower($tipoLargo), 'básico')) {
            return self::TIPO_BASICO;
        }
        return self::TIPO_INTERMEDIO_AVANZADO;
    }
}