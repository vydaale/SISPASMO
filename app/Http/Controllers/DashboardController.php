<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Aspirante;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $alumnosTotal    = Alumno::count();
        $docentesTotal   = Docente::count();
        $aspirantesTotal = Aspirante::count();

        $alumnosActivos = Alumno::whereRaw('LOWER(estatus) = ?', ['activo'])->count();
        $alumnosBaja    = Alumno::whereRaw('LOWER(estatus) = ?', ['baja'])->count();

        return view('administrador.dashboardadmin', compact(
            'alumnosTotal','docentesTotal','aspirantesTotal','alumnosActivos','alumnosBaja'
        ));
    }

    public function metrics(): JsonResponse
    {
        $alumnosTotal    = Alumno::count();
        $docentesTotal   = Docente::count();
        $aspirantesTotal = Aspirante::count();

        $activos = Alumno::whereRaw('LOWER(estatus) = ?', ['activo'])->count();
        $baja    = Alumno::whereRaw('LOWER(estatus) = ?', ['baja'])->count();

        return response()->json([
            'alumnos'    => ['total' => $alumnosTotal, 'activos' => $activos, 'baja' => $baja],
            'docentes'   => $docentesTotal,
            'aspirantes' => $aspirantesTotal,
        ]);
    }
}
