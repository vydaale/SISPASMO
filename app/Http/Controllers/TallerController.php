<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Taller;

class TallerController extends Controller{
    public function index(){
        $talleres = Taller::orderByDesc('id_extracurricular')->paginate(15);
        return view('CRUDTaller.read', compact('talleres'));

    }
    public function create(){
        return view('CRUDTaller.create');
    }
    public function store(Request $request){
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

        Taller::create($request->all());

        return redirect()->route('extracurricular.index')->with('success', 'Taller creado exitosamente.');
    }
    public function edit($id){
        $taller = Taller::findOrFail($id);
        return view('CRUDTaller.update', compact('taller'));
    }
    public function update(Request $request, $id){
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
    public function destroy($id){
        $taller = Taller::findOrFail($id);
        $taller->delete();
        return redirect()->route('extracurricular.index')->with('success', 'Taller eliminado exitosamente.');
    }

}
