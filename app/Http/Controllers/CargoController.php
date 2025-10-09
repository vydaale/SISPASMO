<?php

namespace App\Http\Controllers;

use App\Models\Cargo;

class CargoController extends Controller
{
    public function show(Cargo $cargo)
    {
        return view('cargos.show', compact('cargo'));
    }
}