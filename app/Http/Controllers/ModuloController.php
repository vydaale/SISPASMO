<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Modulo;


class ModuloController extends Controller
{
    public function index()
{
    $modulos = Modulo::orderBy('numero_modulo')   // agrega tus with() si necesitas
        ->paginate(10);                           // <-- importante
    return view('CRUDModulo.read', compact('modulos'));
}

    public function create()
    {
        return view('CRUDModulo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_modulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion' => 'required|string',
            'estatus' => ['required', Rule::in(['activa', 'concluida'])],
            'url' => 'required|String',
        ]);

        Modulo::create($request->all());

        return redirect()->route('modulos.index')->with('success', 'Módulo creado exitosamente.');
    }

    public function edit($id)
    {
        $modulo = Modulo::findOrFail($id);
        return view('CRUDModulo.update', compact('modulo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_modulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion' => 'required|string',
            'estatus' => ['required', Rule::in(['activa', 'concluida'])],
            'url' => 'required|String',
        ]);

        $modulo = Modulo::findOrFail($id);
        $modulo->update($request->all());

        return redirect()->route('modulos.index')->with('success', 'Módulo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $modulo = Modulo::findOrFail($id);
        $modulo->delete();

        return redirect()->route('modulos.index')->with('success', 'Módulo eliminado exitosamente.');
    }
}
