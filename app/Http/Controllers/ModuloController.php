<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Modulo;


class ModuloController extends Controller
{
    public function index()
    {
        $modulos = Modulo::orderBy('numero_modulo')  
            ->paginate(10);                         
        return view('CRUDModulo.read', compact('modulos'));
    }

    public function create()
    {
        return view('CRUDModulo.create');
    }

    public function store(Request $request)
    {
        // 1. Valida los datos que vienen del formulario
        $data = $request->validate([
            'nombre_modulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'duracion' => 'required|string',
            'estatus' => ['required', Rule::in(['activa', 'concluida'])],
            'url' => 'nullable|url|max:200', // 'url' es un mejor validador que 'String' y debe ser nullable si es opcional
        ]);

        // 2. Lógica para el número de módulo autoincrementable
        // Busca el número de módulo más alto que ya existe. Si no hay ninguno, empieza en 0.
        $ultimoNumero = Modulo::max('numero_modulo') ?? 0;
        // Le suma 1 para obtener el nuevo número y lo añade a los datos validados.
        $data['numero_modulo'] = $ultimoNumero + 1;

        // 3. Crea el módulo con todos los datos (incluyendo el nuevo número)
        Modulo::create($data);

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
