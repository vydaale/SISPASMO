<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Contracts\Service\Attribute\Required;

class DocenteController extends Controller{
    public function index(){
        // Lista de docentes con datos del usuario
        $docentes = Docente::with('usuario')->orderByDesc('id_docente')->paginate(15);
        return view('administrador.CRUDDocentes.read', compact('docentes'));
    }
    public function create(){
        return view('administrador.CRUDDocentes.create');
    }
    public function store(Request $request){
        $data = $request->validate([
            // USUARIO
            'nombre'       => ['required','string','max:100'],
            'apellidoP'    => ['required','string','max:100'],
            'apellidoM'    => ['required','string','max:100'],
            'fecha_nac'    => ['required','date'],
            'usuario'      => ['required','string','max:50','unique:usuarios,usuario'],
            'pass'         => ['required','string','min:8','confirmed'], // requiere pass_confirmation
            'genero'       => ['required', Rule::in(['M','F','Otro'])],
            'correo'       => ['required','email','max:100','unique:usuarios,correo'],
            'telefono'     => ['required','string','max:20'],
            'direccion'    => ['required','string','max:100'],
            'id_rol'       => ['required','integer'], // o exists:roles,id_rol si lo manejas por tabla
            // DOCENTE
            'matriculaD'   => ['required','string','max:20','unique:docentes,matriculaD'],
            'especialidad' => ['required','string','max:100'],
            'cedula'        => ['required','string','max:100'],  
            'salario' => ['required', 'decimal:0,2']
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
            Docente::create([
                'matriculaD'   => $data['matriculaD'],
                'especialidad' => $data['especialidad'],
                'cedula'       => $data['cedula'],
                'salario'      => $data['salario'],
                'id_usuario'   => $usuario->id_usuario,
            ]);
        });
        return redirect()->route('docentes.index')->with('success', 'Docente creado exitosamente.');
    }   

    public function edit(Docente $docente){
        $docente->load('usuario');
        return view('administrador.CRUDDocentes.update', compact('docente'));
    }

    public function update(Request $request, Docente $docente){
        $data = $request->validate([
            // USUARIO
            'nombre'       => ['required','string','max:100'],
            'apellidoP'    => ['required','string','max:100'],
            'apellidoM'    => ['required','string','max:100'],
            'fecha_nac'    => ['required','date'],
            'usuario'      => ['required','string','max:50','unique:usuarios,usuario,'.$docente->usuario->id_usuario.',id_usuario'],
            'pass'         => ['nullable','string','min:8','confirmed'], // requiere pass_confirmation
            'genero'       => ['required', Rule::in(['M','F','Otro'])],
            'correo'       => ['required','email','max:100','unique:usuarios,correo,'.$docente->usuario->id_usuario.',id_usuario'],
            'telefono'     => ['required','string','max:20'],
            'direccion'    => ['required','string','max:100'],
            'id_rol'       => ['required','integer'], // o exists:roles,id_rol si lo manejas por tabla
            // DOCENTE
            'matriculaD'   => ['required','string','max:20','unique:docentes,matriculaD,'.$docente->id_docente.',id_docente'],
            'especialidad' => ['required','string','max:100'],
            'cedula'        => ['required','string','max:100'],  
            'salario' => ['required', 'decimal:0,2']
        ]);
        DB::transaction(function () use ($data, $docente) {
            $usuarioData = [
                'nombre'      => $data['nombre'],
                'apellidoP'   => $data['apellidoP'],
                'apellidoM'   => $data['apellidoM'],
                'fecha_nac'   => $data['fecha_nac'],
                'usuario'     => $data['usuario'],
                'genero'      => $data['genero'],
                'correo'      => $data['correo'],
                'telefono'    => $data['telefono'],
                'direccion'   => $data['direccion'],
                'id_rol'      => $data['id_rol'],
            ];
            if (!empty($data['pass'])) {
                $usuarioData['pass'] = Hash::make($data['pass']);
            }
            $docente->usuario->update($usuarioData);
            $docente->update([
                'matriculaD'   => $data['matriculaD'],
                'especialidad' => $data['especialidad'],
                'cedula'       => $data['cedula'],
                'salario'      => $data['salario'],
            ]);
        });
        return redirect()->route('docentes.index')->with('ok', 'Docente actualizado correctamente.');
    }

    public function destroy(Docente $docente){
        DB::transaction(function () use ($docente) {
            // Primero borramos docente para no romper FK, luego Usuario
            $usuario = $docente->usuario;
            $docente->delete();

            if ($usuario) {
                $usuario->delete();
            }
        });

        return redirect()->route('docentes.index')->with('ok', 'docente eliminado.');
    }

}
