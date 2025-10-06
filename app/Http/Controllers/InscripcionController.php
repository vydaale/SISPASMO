<?php

namespace App\Http\Controllers;

use App\Models\Taller as Extracurricular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{
    public function index()
    {
        $alumno = Auth::user()->alumno;

        $inscripcionesIds = $alumno->extracurriculares()->pluck('extracurricular.id_extracurricular');

        $misInscripciones = $alumno->extracurriculares()->orderBy('fecha', 'asc')->get();

        $actividadesDisponibles = Extracurricular::where('estatus', 'Convocatoria')
            ->where('fecha', '>=', now()->toDateString())
            ->whereNotIn('id_extracurricular', $inscripcionesIds) // <-- La clave está aquí
            ->orderBy('fecha', 'asc')
            ->get();

        return view('extracurriculares.read', [
            'misInscripciones' => $misInscripciones,
            'extracurricularesDisponibles' => $actividadesDisponibles
        ]);
    }
    
    public function store(Request $request, Extracurricular $extracurricular)
    {
        $user = Auth::user();
        $alumno = $user->alumno;

        if (!$alumno) {
            return back()->with('error', 'Tu usuario no tiene un perfil de alumno asociado.');
        }

        if ($extracurricular->fecha < now()->toDateString()) {
            return back()->with('error', 'La fecha de esta actividad ya ha pasado. No puedes inscribirte.');
        }

        
        $inscritos = $extracurricular->alumnos()->count();
        if ($inscritos >= $extracurricular->capacidad) {
            return back()->with('error', 'Lo sentimos, ya no hay cupos disponibles para esta actividad.');
        }

        if ($extracurricular->estatus !== 'Convocatoria') {
            return back()->with('error', 'Esta actividad no se encuentra en periodo de inscripción.');
        }

        $yaInscrito = $alumno->extracurriculares()->where('extracurricular.id_extracurricular', $extracurricular->id_extracurricular)->exists();
        if ($yaInscrito) {
            return back()->with('error', 'Ya te encuentras inscrito en esta actividad.');
        }

        $alumno->extracurriculares()->attach($extracurricular->id_extracurricular);

        return back()->with('success', '¡Felicidades! Te has inscrito correctamente a: ' . $extracurricular->nombre_act);
    }

    public function destroy(Extracurricular $extracurricular)
    {
        $alumno = Auth::user()->alumno;

        if ($extracurricular->fecha < now()->toDateString()) {
            return back()->with('error', 'No puedes cancelar la inscripción a una actividad que ya ha ocurrido.');
        }

        $alumno->extracurriculares()->detach($extracurricular->id_extracurricular);

        return back()->with('success', 'Tu inscripción a "' . $extracurricular->nombre_act . '" ha sido cancelada.');
    }
}
