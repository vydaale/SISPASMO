<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Coordinador;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $estatus = $request->query('estatus');
        $q = Cita::query();

        if ($estatus && in_array($estatus, ['Pendiente','Aprobada','Concluida','Cancelada'])) {
            $q->where('estatus', $estatus);
        }

        $citas = $q->with(['aspirante.usuario','coordinador.usuario' => function($q){ $q->select('id_usuario','nombre','apellidoP'); }])
            ->orderBy('fecha_cita')->orderBy('hora_cita')
            ->paginate(15);

        return view('administrador.CRUDCITAS.read', compact('citas','estatus'));
    }

    public function updateStatus(Request $request, Cita $cita)
    {
        $data = $request->validate([
            'estatus' => ['required','in:Pendiente,Aprobada,Concluida,Cancelada']
        ]);

        $coordinadorId = Coordinador::where('id_usuario', auth()->id())->value('id_coordinador')?? Coordinador::min('id_coordinador');

        if (!$coordinadorId) {
            return back()->withErrors(['general' => 'No hay coordinadores disponibles para asignar a la cita.']);
        }

        $cita->estatus = $data['estatus'];
        $cita->id_coordinador = $coordinadorId;
        $cita->save();

        return back()->with('ok', 'Estatus actualizado.');
    }
}
