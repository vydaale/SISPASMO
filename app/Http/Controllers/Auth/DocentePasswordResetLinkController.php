<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class DocentePasswordResetLinkController extends Controller
{
    /*
     * Muestra la vista del formulario para solicitar el restablecimiento de contraseña.
    */
    public function create()
    {
        return view('docente.auth.forgot-password');
    }


    /*
     * Envía el enlace de restablecimiento de contraseña al correo electrónico del Docente.
    */
    public function store(Request $request)
    {
        $request->validate(['correo' => 'required|email']);

        $status = \Password::broker('users')->sendResetLink([
            'correo' => $request->input('correo'),
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            
            return back()->with('status', '¡Listo! Te enviamos un enlace de recuperación a tu correo.');
            
        } else {
                        
            /* Obtenemos el mensaje de error traducido por Laravel */
            $errorMessage = trans($status); 
            
            if ($status === Password::INVALID_USER) {
                 $errorMessage = 'No pudimos encontrar un alumno registrado con ese correo electrónico.';
            }

            /* Devolvemos el mensaje de error usando 'status' */
            return back()->withInput($request->only('correo'))
                         ->with('status', $errorMessage);
        }
    }
}