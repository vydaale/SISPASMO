<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class DocentePasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('docente.auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate(['correo' => 'required|email']);

        $status = \Password::broker('users')->sendResetLink([
            'correo' => $request->input('correo'),
        ]);

        return $status === \Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['correo' => __($status)]);
    }
}