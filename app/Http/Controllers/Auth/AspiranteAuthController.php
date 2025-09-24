<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Aspirante;
use App\Models\User;
use App\Models\Diplomado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AspiranteAuthController extends Controller
{
    public function select()
    {
        return view('aspirante.aspiranteselect');
    }

    public function showRegisterForm()
    {
        $diplomados = Diplomado::orderBy('nombre')->get(['id_diplomado','nombre']);
        return view('aspirante.aspiranteregistro', compact('diplomados'));
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nombre'      => ['required','string','max:100'],
            'apellidoP'   => ['required','string','max:100'],
            'apellidoM'   => ['required','string','max:100'],
            'fecha_nac'   => ['required','date'],
            'genero'      => ['required','in:M,F,Otro'],
            'correo'      => ['required','email','max:100','unique:usuarios,correo'],
            'telefono'    => ['required','string','max:20'],
            'direccion'   => ['required','string','max:100'],
            'password'    => ['required','string','min:8','confirmed'],

            // 👇 ahora vienes con el ID del diplomado, no con texto
            'id_diplomado'=> ['required','integer','exists:diplomados,id_diplomado'],

            'dia'         => ['nullable','date'],
            'acepto'      => ['accepted'],
        ],[
            'acepto.accepted' => 'Debes aceptar el aviso de privacidad.',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $idRol = DB::table('roles')->where('nombre_rol','Aspirante')->value('id_rol')
                    ?? DB::table('roles')->insertGetId(['nombre_rol'=>'Aspirante']);

            $idUsuario = DB::table('usuarios')->insertGetId([
                'nombre'    => $data['nombre'],
                'apellidoP' => $data['apellidoP'],
                'apellidoM' => $data['apellidoM'],
                'fecha_nac' => $data['fecha_nac'],
                'usuario'   => strtolower($data['correo']),
                'pass'      => Hash::make($data['password']), 
                'genero'    => $data['genero'],
                'correo'    => strtolower($data['correo']),
                'telefono'  => $data['telefono'],
                'direccion' => $data['direccion'],
                'id_rol'    => $idRol,
            ]);

            $dip = Diplomado::findOrFail($data['id_diplomado']);

            DB::table('aspirantes')->insert([
                'id_usuario' => $idUsuario,
                'interes'    => $dip->nombre,                     
                'dia'        => $data['dia'] ?? now()->toDateString(),
                'estatus'    => 'activo',
            ]);

            $user = User::find($idUsuario);
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('aspirante.dashboard')->with('ok','¡Registro completado!');
        });
    }

    public function showLoginForm() 
    { 
        return view('aspirante.aspirantelogin'); 
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'correo'   => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::with('rol')->where('correo', $cred['correo'])->first();

        if (!$user || !Hash::check($cred['password'], $user->pass)) {
            return back()->withErrors(['correo' => 'Credenciales inválidas.'])->withInput();
        }
        if (!$user->rol || $user->rol->nombre_rol !== 'Aspirante') {
            return back()->withErrors(['correo' => 'Esta cuenta no es de aspirante.'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('aspirante.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('aspirante.login');
    }
}

