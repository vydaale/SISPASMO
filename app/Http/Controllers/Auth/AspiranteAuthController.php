<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Aspirante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AspiranteAuthController extends Controller
{
    public function select() { return view('aspirante.aspiranteselect'); }

    /* ===== Registro ===== */
    public function showRegisterForm() { return view('aspirante.aspiranteregistro'); }

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
            'interes'     => ['required','string','max:50'],
            'dia'         => ['nullable','date'],
            'acepto'      => ['accepted'],
        ],[
            'acepto.accepted' => 'Debes aceptar el aviso de privacidad.',
        ]);

        return DB::transaction(function () use ($data, $request) {
            // Asegurar rol "Aspirante"
            $idRol = DB::table('roles')->where('nombre_rol','Aspirante')->value('id_rol')
                    ?? DB::table('roles')->insertGetId(['nombre_rol'=>'Aspirante']);

            // Crear usuario (usaremos el correo como "usuario")
            $idUsuario = DB::table('usuarios')->insertGetId([
                'nombre'    => $data['nombre'],
                'apellidoP' => $data['apellidoP'],
                'apellidoM' => $data['apellidoM'],
                'fecha_nac' => $data['fecha_nac'],
                'usuario'   => $data['correo'],             // UNIQUE
                'pass'      => Hash::make($data['password']),
                'genero'    => $data['genero'],
                'correo'    => $data['correo'],
                'telefono'  => $data['telefono'],
                'direccion' => $data['direccion'],
                'id_rol'    => $idRol,
            ]);

            // Crear aspirante
            DB::table('aspirantes')->insert([
                'id_usuario' => $idUsuario,
                'interes'    => $data['interes'],
                'dia'        => $data['dia'] ?? now()->toDateString(),
                'estatus'    => 'activo',
            ]);

            // Login automático
            $user = User::find($idUsuario);
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('aspirante.dashboard')->with('ok','¡Registro completado!');
        });
    }

    /* ===== Login ===== */
    public function showLoginForm() { return view('aspirante.aspirantelogin'); }

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

