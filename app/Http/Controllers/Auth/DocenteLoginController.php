<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DocenteLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('docente.docentelogin');
    }

    public function login(Request $request)
    {
        $request->validate([
            'matricula' => ['required', 'string'],
            'password'  => ['required', 'string'],
        ], [
            'matricula.required' => 'La matrícula es obligatoria.',
            'password.required'  => 'La contraseña es obligatoria.',
        ]);

        $docente = Docente::with('usuario.rol')
                        ->where('matriculaD', $request->matricula)
                        ->first();

        if (!$docente || !$docente->usuario) {
            return back()->withErrors(['matricula' => 'Matrícula no encontrada.'])->withInput();
        }

        $user = $docente->usuario;

        if (!Hash::check($request->password, $user->pass)) {
            return back()->withErrors(['password' => 'Contraseña incorrecta.'])->withInput();
        }

        if (!$user->rol || $user->rol->nombre_rol !== 'Docente') {
            return back()->withErrors(['matricula' => 'No tienes permisos para acceder aquí.'])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('docente.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('docente.login');
    }
}
