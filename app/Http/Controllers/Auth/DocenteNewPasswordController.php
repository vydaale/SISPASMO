<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class DocenteNewPasswordController extends Controller
{
    public function create(Request $request, $token)
    {
        $correo = $request->query('email');
        return view('docente.auth.reset-password', [
            'token'  => $token,
            'correo' => $correo,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'correo'                => 'required|email',
            'password'              => 'required|confirmed|min:8',
        ]);

        $status = Password::broker('users')->reset(
            [
                'correo'                 => $request->correo,
                'password'               => $request->password,
                'password_confirmation'  => $request->password_confirmation,
                'token'                  => $request->token,
            ],
            function ($user) use ($request) {
                $user->forceFill([
                    'pass' => Hash::make($request->password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('docente.login')->with('status', __($status))
            : back()->withErrors(['correo' => __($status)]);
    }
}
