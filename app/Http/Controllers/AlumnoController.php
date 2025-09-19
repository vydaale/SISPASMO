<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\User;
use App\Models\Diplomado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AlumnoController extends Controller
{
    public function index()
    {
        $alumnos = Alumno::with(['usuario', 'diplomado'])->orderByDesc('id_alumno')->paginate(15);
        return view('administrador.CRUDAlumnos.read', compact('alumnos'));
    }

    public function create()
    {
        $diplomados = Diplomado::all();
        return view('administrador.CRUDalumnos.create', compact('diplomados'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => ['required','string','max:100'],
            'apellidoP'    => ['required','string','max:100'],
            'apellidoM'    => ['required','string','max:100'],
            'fecha_nac'    => ['required','date'],
            'usuario'      => ['required','string','max:50','unique:usuarios,usuario'],
            'pass'         => ['required','string','min:8','confirmed'],
            'genero'       => ['required', Rule::in(['M','F','Otro'])],
            'correo'       => ['required','email','max:100','unique:usuarios,correo'],
            'telefono'     => ['required','string','max:20'],
            'direccion'    => ['required','string','max:100'],
            'id_rol'       => ['required','integer'], 

            'matriculaA'   => ['required','string','max:20','unique:alumnos,matriculaA'],
            'id_diplomado' => ['required','integer','exists:diplomados,id_diplomado'],
            'estatus'      => ['required', Rule::in(['activo','baja','egresado'])],
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

            Alumno::create([
                'id_usuario'    => $usuario->id_usuario,
                'matriculaA'    => $data['matriculaA'],
                'id_diplomado'  => $data['id_diplomado'],
                'estatus'       => $data['estatus'],
            ]);
        });

        return redirect()->route('alumnos.index')->with('ok', 'Alumno creado correctamente.');
    }

    public function edit(Alumno $alumno)
    {
        $alumno->load('usuario', 'diplomado');
        $diplomados = Diplomado::all();
        return view('administrador.CRUDalumnos.update', compact('alumno', 'diplomados'));
    }

    public function update(Request $request, Alumno $alumno)
    {
        $alumno->load('usuario');
        $u = $alumno->usuario;

        $data = $request->validate([
            'nombre'       => ['required','string','max:100'],
            'apellidoP'    => ['required','string','max:100'],
            'apellidoM'    => ['required','string','max:100'],
            'fecha_nac'    => ['required','date'],
            'usuario'      => [
                'required','string','max:50',
                Rule::unique('usuarios','usuario')->ignore($u->id_usuario, 'id_usuario'),
            ],
            'pass'         => ['nullable','string','min:8','confirmed'],
            'genero'       => ['required', Rule::in(['M','F','Otro'])],
            'correo'       => [
                'required','email','max:100',
                Rule::unique('usuarios','correo')->ignore($u->id_usuario, 'id_usuario'),
            ],
            'telefono'     => ['required','string','max:20'],
            'direccion'    => ['required','string','max:100'],
            'id_rol'       => ['required','integer'],

            'matriculaA'   => [
                'required','string','max:20',
                Rule::unique('alumnos','matriculaA')->ignore($alumno->id_alumno, 'id_alumno'),
            ],
            'id_diplomado' => ['required','integer','exists:diplomados,id_diplomado'],
            'estatus'      => ['required', Rule::in(['activo','baja','egresado'])],
        ]);

        DB::transaction(function () use ($data, $u, $alumno) {
            $u->fill([
                'nombre'    => $data['nombre'],
                'apellidoP' => $data['apellidoP'],
                'apellidoM' => $data['apellidoM'],
                'fecha_nac' => $data['fecha_nac'],
                'usuario'   => $data['usuario'],
                'genero'    => $data['genero'],
                'correo'    => $data['correo'],
                'telefono'  => $data['telefono'],
                'direccion' => $data['direccion'],
                'id_rol'    => $data['id_rol'],
            ]);

            if (!empty($data['pass'])) {
                $u->pass = Hash::make($data['pass']);
            }
            $u->save();

            $alumno->update([
                'matriculaA'    => $data['matriculaA'],
                'id_diplomado'  => $data['id_diplomado'],
                'estatus'       => $data['estatus'],
            ]);
        });

        return redirect()->route('alumnos.index')->with('ok', 'Alumno actualizado.');
    }

    public function destroy(Alumno $alumno)
    {
        DB::transaction(function () use ($alumno) {
            $usuario = $alumno->usuario;
            $alumno->delete();
            if ($usuario) {
                $usuario->delete();
            }
        });

        return redirect()->route('alumnos.index')->with('ok', 'Alumno eliminado.');
    }
}