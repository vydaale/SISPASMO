<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinador;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CoordinadorController extends Controller
{
    public function index()
    {
        // Lista de coordinadores con datos del usuario
        $coordinadores = Coordinador::with('usuario')->orderByDesc('id_coordinador')->paginate(15);
        return view('administrador.CRUDCoordinadores.read', compact('coordinadores'));
    }
    public function create()
    {
        return view('administrador.CRUDCoordinadores.create');
    }

    public function edit(Coordinador $coordinador)
    {
        // Cargar relación usuario
        $coordinador->load('usuario');
        return view('administrador.CRUDCoordinadores.update', compact('coordinador'));
    }

    public function update(Request $request, Coordinador $coordinador)
    {
        $data = $request->validate([
            // USUARIO
            'nombre'       => ['required', 'string', 'max:100'],
            'apellidoP'    => ['required', 'string', 'max:100'],
            'apellidoM'    => ['required', 'string', 'max:100'],
            'fecha_nac'    => ['required', 'date'],
            'usuario'      => ['required', 'string', 'max:50', Rule::unique('usuarios', 'usuario')->ignore($coordinador->usuario->id_usuario, 'id_usuario')],
            'genero'       => ['required', Rule::in(['M', 'F', 'Otro'])],
            'correo'       => ['required', 'email', 'max:100', Rule::unique('usuarios', 'correo')->ignore($coordinador->usuario->id_usuario, 'id_usuario')],
            'telefono'     => ['required', 'string', 'max:20'],
            'direccion'    => ['required', 'string', 'max:100'],
            // COORDINADOR
            'fecha_ingreso' => ['required', 'date'],
            'estatus'       => ['required', Rule::in(['activo', 'inactivo'])],
        ]);
        DB::transaction(function () use ($data, $coordinador) {
            // Actualizar datos del usuario relacionado
            $coordinador->usuario->update([
                'nombre'      => $data['nombre'],
                'apellidoP'   => $data['apellidoP'],
                'apellidoM'   => $data['apellidoM'],
                'fecha_nac'   => $data['fecha_nac'],
                'usuario'     => $data['usuario'],
                //'pass'        => Hash::make($data['pass']), // No actualizamos pass aquí
                'genero'      => $data['genero'],
                'correo'      => $data['correo'],
                'telefono'    => $data['telefono'],
                'direccion'   => $data['direccion'],
            ]);
            // Actualizar datos del coordinador
            $coordinador->update([
                'fecha_ingreso' => $data['fecha_ingreso'],
                'estatus'       => $data['estatus'],
            ]);
        });
        return redirect()->route('coordinadores.index')->with('success', 'Coordinador actualizado exitosamente.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // USUARIO
            'nombre'       => ['required', 'string', 'max:100'],
            'apellidoP'    => ['required', 'string', 'max:100'],
            'apellidoM'    => ['required', 'string', 'max:100'],
            'fecha_nac'    => ['required', 'date'],
            'usuario'      => ['required', 'string', 'max:50', 'unique:usuarios,usuario'],
            'pass'         => ['required', 'string', 'min:8', 'max:255'],
            'genero'       => ['required', Rule::in(['M', 'F', 'Otro'])],
            'correo'       => ['required', 'email', 'max:100', 'unique:usuarios,correo'],
            'telefono'     => ['required', 'string', 'max:20'],
            'direccion'    => ['required', 'string', 'max:100'],
            // COORDINADOR
            'fecha_ingreso' => ['required', 'date'],
            'estatus'       => ['required', Rule::in(['activo', 'inactivo'])],
        ]);
        DB::transaction(function () use ($data) {
            // Crear el usuario primero
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
                'id_rol'      => 3, // Rol de Coordinador
            ]);
            // Luego crear el coordinador con el id_usuario generado
            Coordinador::create([
                'id_usuario'    => $usuario->id_usuario,
                'fecha_ingreso' => $data['fecha_ingreso'],
                'estatus'       => $data['estatus'],
            ]);
        });
        return redirect()->route('coordinadores.index')->with('success', 'Coordinador creado exitosamente.');
    }

    public function destroy(Coordinador $coordinador)
    {
        DB::transaction(function () use ($coordinador) {
            $usuario = $coordinador->usuario; // relación belongsTo
            $coordinador->delete();           // borra el coordinador primero

            // Si quieres eliminar también la cuenta de usuario:
            if ($usuario) {
                $usuario->delete();
            }
        });

        return redirect()->route('coordinadores.index')->with('ok', 'Coordinador eliminado.');
    }
}
