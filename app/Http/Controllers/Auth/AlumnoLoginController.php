<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AlumnoLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('alumno.loginalumno');
    }

    public function login(Request $request)
    {
        // Validación simple
        $request->validate([
            'matricula' => ['required', 'string'],
            'password'  => ['required', 'string'],
        ], [
            'matricula.required' => 'La matrícula es obligatoria.',
            'password.required'  => 'La contraseña es obligatoria.',
        ]);

        // Buscar alumno por matrícula
        $alumno = Alumno::with('usuario.rol')
                        ->where('matriculaA', $request->matricula)
                        ->first();

        if (!$alumno || !$alumno->usuario) {
            return back()
                ->withErrors(['matricula' => 'Matrícula no encontrada.'])
                ->withInput();
        }

        $user = $alumno->usuario;

        // Validar contraseña
        if (!Hash::check($request->password, $user->pass)) {
            return back()
                ->withErrors(['password' => 'Contraseña incorrecta.'])
                ->withInput();
        }

        // Validar rol de alumno
        if (!$user->rol || $user->rol->nombre_rol !== 'Alumno') {
            return back()
                ->withErrors(['matricula' => 'El usuario no tiene rol de Alumno.'])
                ->withInput();
        }

        // Iniciar sesión
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('alumno.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('alumno.login');
    }
}
