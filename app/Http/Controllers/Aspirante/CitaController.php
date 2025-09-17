<?php

namespace App\Http\Controllers\Aspirante;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Aspirante;
use App\Models\Coordinador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CitaController extends Controller
{
    // Mis citas
    public function index()
    {
        $aspiranteId = Aspirante::where('id_usuario', auth()->id())->value('id_aspirante');

        $citas = Cita::where('id_aspirante', $aspiranteId)
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_cita')
            ->paginate(10);

        return view('aspirante.citas.index', compact('citas'));
    }

    // Form crear
    public function create()
    {
        return view('aspirante.citas.create');
    }

    // Guardar
    public function store(Request $request)
    {
        $aspiranteId = Aspirante::where('id_usuario', auth()->id())->value('id_aspirante');
        if (!$aspiranteId) {
            return back()->withErrors(['general' => 'No se encontró tu perfil de aspirante.'])->withInput();
        }

        // Validación básica de fecha/hora
        $data = $request->validate([
            'fecha_cita' => ['required', 'date', 'after_or_equal:today'],
            'hora_cita'  => ['required', 'date_format:H:i'],
        ]);

        // Reglas de negocio: 1 sola cita activa por aspirante
        $tieneActiva = Cita::where('id_aspirante', $aspiranteId)
            ->whereIn('estatus', ['Pendiente','Aprobada'])
            ->exists();

        if ($tieneActiva) {
            return back()->withErrors(['general' => 'Ya tienes una cita activa. Cancélala o espera a que concluya para agendar otra.'])->withInput();
        }

        // Evitar choque de horario (fecha+hora ocupadas por otra cita activa)
        $ocupada = Cita::whereDate('fecha_cita', $data['fecha_cita'])
            ->where('hora_cita', $data['hora_cita'])
            ->whereIn('estatus', ['Pendiente','Aprobada'])
            ->exists();

        if ($ocupada) {
            return back()->withErrors(['general' => 'Ese horario ya está ocupado. Elige otro día/hora.'])->withInput();
        }

        // Elegimos automáticamente un coordinador (requerido por tu tabla)
        $coordinadorId = Coordinador::min('id_coordinador'); // usa el primero disponible
        if (!$coordinadorId) {
            return back()->withErrors(['general' => 'No hay coordinadores registrados para asignar a la cita.'])->withInput();
        }

        // Crear cita (lugar fijo)
        Cita::create([
            'fecha_cita'   => $data['fecha_cita'],
            'hora_cita'    => $data['hora_cita'] . ':00',
            'estatus'      => 'Pendiente',
            'lugar'        => 'Facultad de Medicina de la UAEM',
            'id_aspirante' => $aspiranteId,
            'id_coordinador' => $coordinadorId,
        ]);

        return redirect()->route('aspirante.citas.index')->with('ok', 'Cita creada con éxito.');
    }

    // Cancelar mi cita
    public function cancel(Cita $cita)
    {
        $aspiranteId = Aspirante::where('id_usuario', auth()->id())->value('id_aspirante');

        if ($cita->id_aspirante !== $aspiranteId) {
            abort(403);
        }

        if (in_array($cita->estatus, ['Pendiente','Aprobada'])) {
            $cita->estatus = 'Cancelada';
            $cita->save();
        }

        return back()->with('ok', 'Cita cancelada.');
    }
}
