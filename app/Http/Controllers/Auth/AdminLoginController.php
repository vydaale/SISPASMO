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
        $messages = [
            'usuario.required' => 'El campo usuario no puede estar vacío.',
            'usuario.email' => 'El usuario debe ser un correo válido).',
            
            'password.required' => 'La contraseña no puede estar vacía.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe incluir al menos una letra mayúscula, una minúscula, un número y un símbolo (#$%&/).',
        ];

        // Validación de usuario y contraseña
        $credentials = $request->validate([
            'usuario' => ['required', 'email', 'string'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',      // mayúscula
                'regex:/[a-z]/',      // minúscula
                'regex:/[0-9]/',      // número
                'regex:/[#\$%&\/]/',  // símbolo
            ],
        ], $messages);

        if (Auth::attempt(
            ['usuario' => $credentials['usuario'], 'password' => $credentials['password']],
            $request->boolean('remember')
        )) {
            $request->session()->regenerate();

            $rol = Auth::user()->rol?->nombre_rol;
            if (!in_array($rol, ['Administrador', 'Coordinador'])) {
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
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
