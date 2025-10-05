<?php

namespace App\Http\Controllers;

use App\Models\Taller as Extracurricular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{
    /**
     * Muestra la lista de actividades disponibles para inscripción.
     */
    public function index()
    {
        // Obtenemos el perfil del alumno autenticado
        $alumno = Auth::user()->alumno;

        // 1. Obtenemos las IDs de las actividades en las que el alumno ya está inscrito
        $inscripcionesIds = $alumno->extracurriculares()->pluck('extracurricular.id_extracurricular');

        // 2. Obtenemos la colección completa de sus inscripciones
        $misInscripciones = $alumno->extracurriculares()->orderBy('fecha', 'asc')->get();

        // 3. Obtenemos las actividades disponibles, EXCLUYENDO aquellas en las que ya está inscrito
        $actividadesDisponibles = Extracurricular::where('estatus', 'Convocatoria')
            ->where('fecha', '>=', now()->toDateString())
            ->whereNotIn('id_extracurricular', $inscripcionesIds) // <-- La clave está aquí
            ->orderBy('fecha', 'asc')
            ->get();

        // 4. Enviamos ambas variables a la vista
        return view('extracurriculares.read', [
            'misInscripciones' => $misInscripciones,
            'extracurricularesDisponibles' => $actividadesDisponibles
        ]);
    }
    /**
     * Procesa la inscripción de un alumno a una actividad.
     */
    public function store(Request $request, Extracurricular $extracurricular)
    {
        // 1. Obtener al USUARIO autenticado y LUEGO a su perfil de ALUMNO
        $user = Auth::user();
        $alumno = $user->alumno; // <--- ¡AQUÍ ESTÁ EL CAMBIO CLAVE!

        // Es una buena práctica verificar que el usuario tenga un perfil de alumno
        if (!$alumno) {
            return back()->with('error', 'Tu usuario no tiene un perfil de alumno asociado.');
        }

        // === VALIDACIONES ===

        // 2. Validación: ¿La fecha ya pasó?
        if ($extracurricular->fecha < now()->toDateString()) {
            return back()->with('error', 'La fecha de esta actividad ya ha pasado. No puedes inscribirte.');
        }

        // 3. Validación: ¿Aún hay cupo?
        $inscritos = $extracurricular->alumnos()->count();
        if ($inscritos >= $extracurricular->capacidad) {
            return back()->with('error', 'Lo sentimos, ya no hay cupos disponibles para esta actividad.');
        }

        // 4. Validación: ¿El estatus es 'Convocatoria'?
        if ($extracurricular->estatus !== 'Convocatoria') {
            return back()->with('error', 'Esta actividad no se encuentra en periodo de inscripción.');
        }

        // 5. Validación: ¿El alumno ya está inscrito?
        // CORRECCIÓN: Usamos la llave primaria correcta -> id_extracurricular
        $yaInscrito = $alumno->extracurriculares()->where('extracurricular.id_extracurricular', $extracurricular->id_extracurricular)->exists();
        if ($yaInscrito) {
            return back()->with('error', 'Ya te encuentras inscrito en esta actividad.');
        }

        // === INSCRIPCIÓN ===
        // Si todo pasa, inscribimos.

        // CORRECCIÓN: Usamos la llave primaria correcta -> id_extracurricular
        $alumno->extracurriculares()->attach($extracurricular->id_extracurricular);

        return back()->with('success', '¡Felicidades! Te has inscrito correctamente a: ' . $extracurricular->nombre_act);
    }

    public function destroy(Extracurricular $extracurricular)
{
    // Obtenemos el perfil del alumno autenticado
    $alumno = Auth::user()->alumno;

    // Validación Opcional: No permitir cancelar si la actividad ya pasó
    if ($extracurricular->fecha < now()->toDateString()) {
        return back()->with('error', 'No puedes cancelar la inscripción a una actividad que ya ha ocurrido.');
    }

    // Usamos el método detach() para eliminar el registro de la tabla intermedia.
    // Es el opuesto a attach().
    $alumno->extracurriculares()->detach($extracurricular->id_extracurricular);

    return back()->with('success', 'Tu inscripción a "' . $extracurricular->nombre_act . '" ha sido cancelada.');
}
}
