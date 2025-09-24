<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Aspirante;

class AspiranteController extends Controller
{
    public function index()
    {
        $aspirantes = Aspirante::with('usuario')->orderByDesc('id_aspirante')->paginate(15);
        return view('administrador.CRUDAspirantes.read', compact('aspirantes'));
    }
    public function create(){
        return view('administrador.CRUDAspirantes.create');
    }

    public function edit(Aspirante $aspirante)
    {
        $aspirante->load('usuario');
        return view('administrador.CRUDAspirantes.update', compact('aspirante'));
    }

    public function update(Request $request, Aspirante $aspirante)
    {
        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:100'],
            'apellidoP'    => ['required', 'string', 'max:100'],
            'apellidoM'    => ['required', 'string', 'max:100'],
            'fecha_nac'    => ['required', 'date'],
            'usuario'      => ['required', 'string', 'max:50', Rule::unique('usuarios','usuario')->ignore($aspirante->usuario->id_usuario,'id_usuario')],
            'genero'       => ['required', Rule::in(['M', 'F', 'Otro'])],
            'correo'       => ['required', 'email', 'max:100', Rule::unique('usuarios','correo')->ignore($aspirante->usuario->id_usuario,'id_usuario')],
            'telefono'     => ['required', 'string', 'max:20'],
            'direccion'    => ['required', 'string', 'max:100'],
            'interes' => ['required', 'string', 'max:50'],             
            'dia'     => ['required', 'date'],
            'estatus' => ['required', Rule::in(['activo', 'rechazado'])],
        ]);
        DB::transaction(function () use ($data, $aspirante) {
            $aspirante->usuario->update([
                'nombre'      => $data['nombre'],
                'apellidoP'   => $data['apellidoP'],
                'apellidoM'   => $data['apellidoM'],
                'fecha_nac'   => $data['fecha_nac'],
                'usuario'     => $data['usuario'],
                'genero'      => $data['genero'],
                'correo'      => $data['correo'],
                'telefono'    => $data['telefono'],
                'direccion'   => $data['direccion'],
            ]);
            $aspirante->update([
                'interes'    => $data['interes'],
                'dia'        => $data['dia'],
                'estatus'    => $data['estatus'],
            ]);
        });
        return redirect()->route('aspirantes.index')->with('success', 'Aspirante actualizado exitosamente.');
    }

    public function store(Request $request){
        $data = $request->validate([
            // USUARIO
            'nombre'       => ['required', 'string', 'max:100'],
            'apellidoP'    => ['required', 'string', 'max:100'],
            'apellidoM'    => ['required', 'string', 'max:100'],
            'fecha_nac'    => ['required', 'date'],
            'usuario'      => ['required', 'string', 'max:50', 'unique:usuarios,usuario'],
            'pass'         => ['required', 'string', 'min:8', 'confirmed'],
            'genero'       => ['required', Rule::in(['M', 'F', 'Otro'])],
            'correo'       => ['required', 'email', 'max:100', 'unique:usuarios,correo'],
            'telefono'     => ['required', 'string', 'max:20'],
            'direccion'    => ['required', 'string', 'max:100'],
            'id_rol'       => ['required', 'integer'], 
            'interes' => ['required', 'string', 'max:50'],             
            'dia'     => ['required', 'date'],
            'estatus' => ['required', Rule::in(['activo', 'rechazado'])],
        ]);
        DB::transaction(function () use ($data) {
            $usuario = User::create([
                'nombre'      => $data['nombre'],
                'apellidoP'   => $data['apellidoP'],
                'apellidoM'   => $data['apellidoM'],
                'fecha_nac'   => $data['fecha_nac'],
                'usuario'     => $data['usuario'],
                'pass'        => Hash::make($data['pass']),
                'genero'      => $data['genero'],
                'correo'      => $data['correo'],
                'telefono'    => $data['telefono'],
                'direccion'   => $data['direccion'],
                'id_rol'      => $data['id_rol'],
            ]);
            Aspirante::create([
                'id_usuario' => $usuario->id_usuario,
                'interes'    => $data['interes'],
                'dia'        => $data['dia'],
                'estatus'    => $data['estatus'],
            ]);
        });
        return redirect()->route('aspirantes.index')->with('success', 'Aspirante creado exitosamente.');
    }

    public function destroy(Aspirante $aspirante)
    {
        DB::transaction(function () use ($aspirante) {
            $usuario = $aspirante->usuario;
            $aspirante->delete();

            if ($usuario) {
                $usuario->delete();
            }
        });

        return redirect()->route('aspirantes.index')->with('ok', 'Aspirante eliminado.');
    }

    public function dashboard()
    {
        $userId = auth()->user()->id_usuario;
        $aspirante = Aspirante::where('id_usuario', $userId)->with('usuario')->firstOrFail();

        return view('aspirante.dashboardaspirante', compact('aspirante'));
    }
}
