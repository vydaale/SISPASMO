<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Taller;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InicioActividadSimple;
use App\Models\Alumno;


class TallerController extends Controller
{
    public function index()
    {
        $talleres = Taller::orderByDesc('id_extracurricular')->paginate(15);
        return view('CRUDTaller.read', compact('talleres'));
    }
    public function create()
    {
        return view('CRUDTaller.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre_act'   => 'required|string|max:255',
            'responsable'  => 'required|string|max:255',
            'fecha'        => 'required|date',
            'tipo'         => ['required', Rule::in(['Taller', 'Practica'])],
            'hora_inicio'  => 'required|date_format:H:i',
            'hora_fin'     => 'required|date_format:H:i|after:hora_inicio',
            'lugar'        => 'required|string|max:255',
            'modalidad'    => ['required', Rule::in(['Presencial', 'Virtual'])],
            'estatus'      => ['required', Rule::in(['Finalizada', 'Convocatoria', 'En proceso'])],
            'capacidad'    => 'required|integer|min:1',
            'descripcion'  => 'nullable|string',
            'material'     => 'nullable|string',
            'url'          => 'nullable|url',
        ]);

        $taller = Taller::create($request->all());

        // === Notificar automáticamente a TODOS los alumnos activos ===
        $this->notificarATodosLosAlumnos($taller);

        return redirect()->route('extracurricular.index')->with('success', 'Taller creado y notificado exitosamente.');
    }
    public function edit($id)
    {
        $taller = Taller::findOrFail($id);
        return view('CRUDTaller.update', compact('taller'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_act' => 'required|string|max:255',
            'responsable' => 'required|string|max:255',
            'fecha' => 'required|date',
            'tipo' => ['required', Rule::in(['Taller', 'Practica'])],
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'lugar' => 'required|string|max:255',
            'modalidad' => ['required', Rule::in(['Presencial', 'Virtual'])],
            'estatus' => ['required', Rule::in(['Finalizada', 'Convocatoria', 'En proceso'])],
            'capacidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'material' => 'nullable|string',
            'url' => 'nullable|url',
        ]);
        $taller = Taller::findOrFail($id);
        $taller->update($request->all());
        return redirect()->route('extracurricular.index')->with('success', 'Taller actualizado exitosamente.');
    }
    public function destroy($id)
    {
        $taller = Taller::findOrFail($id);
        $taller->delete();
        return redirect()->route('extracurricular.index')->with('success', 'Taller eliminado exitosamente.');
    }

    private function notificarATodosLosAlumnos(Taller $taller): void
    {
        // 1) Destinatarios: TODOS los alumnos activos
        $alumnos = Alumno::where('estatus', 'activo')
            ->with('usuario')                   // relación Alumno->usuario
            ->get()
            ->pluck('usuario')
            ->filter()
            ->unique('id_usuario');             // evita duplicados por si acaso

        if ($alumnos->isEmpty()) {
            return; // no hay a quién enviar (no falla)
        }

        // 2) Preparar datos del taller para la notificación
        // Lugar mostrado: si es virtual, prioriza el enlace; si no, el aula/lugar
        $lugar = $taller->modalidad === 'Virtual'
            ? ($taller->url ?: 'Enlace por confirmar')
            : ($taller->lugar ?: 'Por confirmar');

        // Fecha (si casteaste a date, usa ->format)
        $fecha = $taller->fecha instanceof \Carbon\Carbon
            ? $taller->fecha->format('Y-m-d')
            : (string) $taller->fecha;

        // 3) Crear la notificación (reusa tu InicioActividadSimple)
        $noti = new \App\Notifications\InicioActividadSimple(
            nombreActividad: $taller->nombre_act,
            fecha: $fecha,
            hora: $taller->hora_inicio,
            lugar: $lugar,
            docente: $taller->responsable,
            instrucciones: $taller->material ?: $taller->descripcion, // lo que quieras mostrar
            urlDetalle: $taller->url ?: null // o pon ruta a un "show" si la tienes
        );

        // 4) Enviar a todos
        Notification::send($alumnos, $noti);
    }
}
