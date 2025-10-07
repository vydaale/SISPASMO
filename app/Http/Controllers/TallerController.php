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
    // 1) Armar valores mostrables
    $lugar = $taller->modalidad === 'Virtual'
        ? ($taller->url ?: 'Enlace por confirmar')
        : ($taller->lugar ?: 'Por confirmar');

    // Si casteas fecha en el modelo, esto siempre será Carbon
    $fecha = $taller->fecha instanceof \Carbon\Carbon
        ? $taller->fecha->format('Y-m-d')
        : (string) $taller->fecha;

    // 2) Instancia única de la notificación (reutilizable)
    $noti = new InicioActividadSimple(
        nombreActividad: $taller->nombre_act,
        fecha: $fecha,
        hora: $taller->hora_inicio,
        lugar: $lugar,
        docente: $taller->responsable,
        instrucciones: $taller->material ?: $taller->descripcion,
        urlDetalle: $taller->url ?: null
    );

    // 3) Enviar en chunks para no saturar memoria ni duplicar
    \App\Models\Alumno::where('estatus', 'activo')
        ->with('usuario:id_usuario,nombre,correo') // trae lo necesario del notifiable
        ->select('id_alumno','id_usuario')         // columnas mínimas en alumnos
        ->chunkById(500, function ($chunk) use ($noti) {
            $usuarios = $chunk->pluck('usuario')->filter()->unique('id_usuario');
            if ($usuarios->isNotEmpty()) {
                \Illuminate\Support\Facades\Notification::send($usuarios, $noti);
                // Gracias a shouldQueue() en tu Notification:
                // - 'database' se guarda ya
                // - 'mail' se va a la cola
            }
        }, 'id_alumno');
}
}
