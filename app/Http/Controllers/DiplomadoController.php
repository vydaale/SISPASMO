<?php

namespace App\Http\Controllers;

use App\Models\Diplomado;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DiplomadoController extends Controller
{
    public function index()
    {
        $diplomados = Diplomado::orderByDesc('id_diplomado')->paginate(15);
        return view('administrador.CRUDDiplomados.read', compact('diplomados'));
    }

    public function create()
    {
        return view('administrador.CRUDDiplomados.create');
    }

    public function store(Request $request)
    {
        $tiposPermitidos = ['basico', 'intermedio y avanzado']; // 🔧 igual que Blade y DB

        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:100'],
            'grupo'        => ['required', 'string', 'max:50'],
            'tipo'         => ['required', 'string', Rule::in($tiposPermitidos)],
            'capacidad'    => ['required', 'integer', 'min:1', 'max:1000'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
        ]);

        DB::transaction(function () use ($data) {
            Diplomado::create($data);
        });

        return redirect()->route('admin.diplomados.index')->with('ok', 'Diplomado creado correctamente.');
    }


    public function edit(Diplomado $diplomado)
    {
        return view('administrador.CRUDDiplomados.update', compact('diplomado'));
    }

    public function update(Request $request, Diplomado $diplomado)
    {
        $tiposPermitidos = ['basico', 'intermedio y avanzado']; // 🔧

        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:100'],
            'grupo'        => ['required', 'string', 'max:50'],
            'tipo'         => ['required', 'string', Rule::in($tiposPermitidos)],
            'capacidad'    => ['required', 'integer', 'min:1', 'max:1000'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
        ]);

        DB::transaction(function () use ($data, $diplomado) {
            $diplomado->update($data);
        });

        return redirect()->route('admin.diplomados.index')->with('ok', 'Diplomado actualizado correctamente.');
    }

    public function destroy(Diplomado $diplomado)
    {
        DB::transaction(function () use ($diplomado) {
            $diplomado->delete();
        });

        return redirect()->route('admin.diplomados.index')->with('ok', 'Diplomado eliminado correctamente.');
    }
}