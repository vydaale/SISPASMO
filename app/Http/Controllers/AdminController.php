<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administrador;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->rol->nombre_rol !== 'Administrador') {
                abort(403, 'Acción no autorizada. No tienes permisos de Administrador.');
            }
            return $next($request);
        });
    }

    public function index(){
        $admin=Administrador::with('usuario')->orderByDesc('id_admin')->paginate(15);
        return view('CRUDAdmin.read', compact('admin'));
    }
    
    public function create(){
        return view('CRUDAdmin.create');
    }

    public function edit(Administrador $admin){
        $admin->load('usuario');
        return view('CRUDAdmin.update', compact('admin'));
    }

    public function store(Request $request){
        $data=$request->validate([
            'nombre'       => ['required', 'string', 'max:100'],
            'apellidoP'    => ['required', 'string', 'max:100'],
            'apellidoM'    => ['required', 'string', 'max:100'],
            'fecha_nac'    => ['required', 'date'],
            'usuario'      => ['required', 'string', 'max:50', 'unique:usuarios,usuario'],
            'pass'         => ['required', 'string', 'min:8', 'max:255'],
            'genero'       => ['required', Rule::in(['M','F','Otro'])],
            'correo'       => ['required', 'email', 'max:100', 'unique:usuarios,correo'],
            'telefono'     => ['required', 'string', 'max:20'],
            'direccion'    => ['required', 'string', 'max:100'],
            'fecha_ingreso'=> ['required', 'date'],
            'rol'=>['required', 'string', 'max:50'],
            'estatus'      => ['required', Rule::in(['activo','inactivo'])],
        ]);
        DB::transaction(function() use ($data){
            $user=User::create([
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
                'id_rol'      => 1,
                'tipo_usuario'=> 'admin',
            ]);
            Administrador::create([
                'id_usuario'   => $user->id_usuario,
                'fecha_ingreso'=> $data['fecha_ingreso'],
                'estatus'      => $data['estatus'],
            ]);
        });
        return redirect()->route('admin.index')->with('success','Administrador creado exitosamente.');
    }

    public function update(){
        $data=request()->validate([
            'nombre'       => ['required', 'string', 'max:100'],
            'apellidoP'    => ['required', 'string', 'max:100'],
            'apellidoM'    => ['required', 'string', 'max:100'],
            'fecha_nac'    => ['required', 'date'],
            'usuario'      => ['required', 'string', 'max:50', Rule::unique('usuarios','usuario')->ignore(request('id_usuario'),'id_usuario')],
            'pass'         => ['nullable', 'string', 'min:8', 'max:255'],
            'genero'       => ['required', Rule::in(['M','F','Otro'])],
            'correo'       => ['required', 'email', 'max:100', Rule::unique('usuarios','correo')->ignore(request('id_usuario'),'id_usuario')],
            'telefono'     => ['required', 'string', 'max:20'],
            'direccion'    => ['required', 'string', 'max:100'],
            'fecha_ingreso'=> ['required', 'date'],
            'rol'=>['required', 'string', 'max:50'],
            'estatus'      => ['required', Rule::in(['activo','inactivo'])],
        ]);
        DB::transaction(function() use ($data){
            $user=User::where('id_usuario', request('id_usuario'))->first();
            $user->nombre      = $data['nombre'];
            $user->apellidoP   = $data['apellidoP'];
            $user->apellidoM   = $data['apellidoM'];
            $user->fecha_nac   = $data['fecha_nac'];
            $user->usuario     = $data['usuario'];
            if(!empty($data['pass'])){
                $user->pass    = Hash::make($data['pass']);
            }
            $user->genero      = $data['genero'];
            $user->correo      = $data['correo'];
            $user->telefono    = $data['telefono'];
            $user->direccion   = $data['direccion'];
            $user->save();
            $admin=Administrador::where('id_usuario', request('id_usuario'))->first();
            $admin->fecha_ingreso= $data['fecha_ingreso'];
            $admin->estatus      = $data['estatus'];
            $admin->save();
        });
        return redirect()->route('admin.index')->with('ok','Administrador actualizado exitosamente.');
    }

    public function destroy(Administrador $admin)
    {
        DB::transaction(function () use ($admin) {
            $usuario = $admin->usuario; 
            $admin->delete();           

            if ($usuario) {
                $usuario->delete();
            }
        });

        return redirect()->route('admin.index')->with('ok', 'Coordinador eliminado.');
    }


}
