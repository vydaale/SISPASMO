<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;             
use Illuminate\Support\Facades\DB;                
use Illuminate\Validation\ValidationException;   

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('administrador.adminlogin');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario'  => ['required','string'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt(
            ['usuario' => $credentials['usuario'], 'password' => $credentials['password']],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            $rol = Auth::user()->rol?->nombre_rol;
            if (!in_array($rol, ['Administrador','Coordinador'])) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'usuario' => 'No tienes permisos para acceder aquí.',
                ]);
            }

            DB::table('accesos')->insert([
                'id_usuario'   => Auth::id(),
                'fecha_acceso' => now(),
                'ip_origen'    => $request->ip(),
                'descripcion'  => 'Login panel admin/coordinador',
            ]);

            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'usuario' => 'Credenciales inválidas.',
        ]);

        if (Auth::attempt(['usuario' => $credentials['usuario'], 'password' => $credentials['password']])) {
            dd('Login exitoso. Redirigiendo...');
        } else {
            dd('Login fallido. Credenciales inválidas.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
