<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\FichaMedica;
use App\Models\Alergia;
use App\Models\Enfermedad;
use App\Models\ContactoEmergencia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class FichaMedicaController extends Controller
{
    public function index()
    {
        $fichas = FichaMedica::with(['alumno'])
            ->orderByDesc('id_ficha')
            ->paginate(15);

        return view('CRUDMedica.readAll', compact('fichas'));
    }

    public function show(FichaMedica $ficha)
    {
        $ficha->load(['alergias', 'enfermedades', 'contacto', 'alumno']);
        return view('CRUDMedica.show', compact('ficha'));
    }

    public function destroy(FichaMedica $ficha)
    {
        DB::transaction(function () use ($ficha) {
            $ficha->alergias?->delete();
            $ficha->enfermedades?->delete();
            $ficha->contacto?->delete();
            $ficha->delete();
        });

        return redirect()->route('fichasmedicas.index')->with('ok', 'Ficha médica eliminada.');
    }

    public function showMine()
    {
        $user = Auth::user();
        abort_unless($user && $user->alumno, 403);

        $ficha = $user->alumno->fichaMedica()
            ->with(['alergias', 'enfermedades', 'contacto', 'alumno'])
            ->first();

        if (!$ficha) {
            return redirect()->route('mi_ficha.create');
        }

        abort_if($ficha->id_alumno !== $user->alumno->id_alumno, 403);

        return view('CRUDMedica.read', compact('ficha'));
    }

    public function createMine()
    {
        $user = Auth::user();
        abort_unless($user && $user->alumno, 403);

        if ($user->alumno->fichaMedica) {
            return redirect()->route('mi_ficha.edit');
        }

        return view('CRUDMedica.create'); 
    }

    public function storeMine(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->alumno, 403);

        abort_if($user->alumno->fichaMedica, 403, 'Ya cuentas con una ficha médica.');

        $data = $request->validate([
            'alergias.polvo'                 => ['nullable', 'boolean'],
            'alergias.polen'                 => ['nullable', 'boolean'],
            'alergias.alimentos'             => ['nullable', 'boolean'],
            'alergias.alimentos_detalle'     => ['nullable', 'string', 'max:255'],
            'alergias.animales'              => ['nullable', 'boolean'],
            'alergias.animales_detalle'      => ['nullable', 'string', 'max:255'],
            'alergias.insectos'              => ['nullable', 'boolean'],
            'alergias.insectos_detalle'      => ['nullable', 'string', 'max:255'],
            'alergias.medicamentos'          => ['nullable', 'boolean'],
            'alergias.medicamentos_detalle'  => ['nullable', 'string', 'max:255'],
            'alergias.otro'                  => ['nullable', 'boolean'],
            'alergias.otro_detalle'          => ['nullable', 'string', 'max:255'],

            'enfermedades.enfermedad_cronica'          => ['nullable', 'boolean'],
            'enfermedades.enfermedad_cronica_detalle'  => ['nullable', 'string', 'max:255'],
            'enfermedades.toma_medicamentos'           => ['nullable', 'boolean'],
            'enfermedades.toma_medicamentos_detalle'   => ['nullable', 'string', 'max:255'],
            'enfermedades.visita_medico'               => ['nullable', 'boolean'],
            'enfermedades.visita_medico_detalle'       => ['nullable', 'string', 'max:255'],
            'enfermedades.nombre_medico'               => ['nullable', 'string', 'max:100'],
            'enfermedades.telefono_medico'             => ['nullable', 'string', 'max:20'],

            'contacto.nombre'     => ['nullable', 'string', 'max:50'],
            'contacto.apellidos'  => ['nullable', 'string', 'max:50'],
            'contacto.domicilio'  => ['nullable', 'string', 'max:100'],
            'contacto.telefono'   => ['nullable', 'string', 'max:50'],
            'contacto.parentesco' => ['nullable', 'string', 'max:50'],
            'contacto.institucion' => ['required', \Illuminate\Validation\Rule::in(['IMSS', 'Cruz Roja', 'Privado'])],
        ]);

        DB::transaction(function () use ($data, $user) {
            $alergias = \App\Models\Alergia::create($data['alergias'] ?? []);
            $enfs     = \App\Models\Enfermedad::create($data['enfermedades'] ?? []);
            $contacto = \App\Models\ContactoEmergencia::create($data['contacto'] ?? []);

            \App\Models\FichaMedica::create([
                'id_alumno'       => $user->alumno->id_alumno, 
                'id_alergias'     => $alergias->id_alergias,
                'id_enfermedades' => $enfs->id_enfermedades,
                'id_contacto'     => $contacto->id_contacto,
            ]);
        });

        return redirect()->route('mi_ficha.show')->with('ok', 'Ficha médica creada correctamente.');
    }

    public function editMine()
    {
        $user = Auth::user();
        abort_unless($user && $user->alumno, 403);

        $ficha = $user->alumno->fichaMedica;
        if (!$ficha) {
            return redirect()->route('mi_ficha.create');
        }

        abort_if($ficha->id_alumno !== $user->alumno->id_alumno, 403);

        $ficha->load(['alergias', 'enfermedades', 'contacto', 'alumno']);
        return view('CRUDMedica.update', compact('ficha'));
    }

    public function updateMine(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->alumno, 403);

        $ficha = $user->alumno->fichaMedica;
        if (!$ficha) {
            return redirect()->route('mi_ficha.create');
        }

        abort_if($ficha->id_alumno !== $user->alumno->id_alumno, 403);

        $data = $request->validate([
            'alergias.polvo'                 => ['nullable', 'boolean'],
            'alergias.polen'                 => ['nullable', 'boolean'],
            'alergias.alimentos'             => ['nullable', 'boolean'],
            'alergias.alimentos_detalle'     => ['nullable', 'string', 'max:255'],
            'alergias.animales'              => ['nullable', 'boolean'],
            'alergias.animales_detalle'      => ['nullable', 'string', 'max:255'],
            'alergias.insectos'              => ['nullable', 'boolean'],
            'alergias.insectos_detalle'      => ['nullable', 'string', 'max:255'],
            'alergias.medicamentos'          => ['nullable', 'boolean'],
            'alergias.medicamentos_detalle'  => ['nullable', 'string', 'max:255'],
            'alergias.otro'                  => ['nullable', 'boolean'],
            'alergias.otro_detalle'          => ['nullable', 'string', 'max:255'],

            'enfermedades.enfermedad_cronica'          => ['nullable', 'boolean'],
            'enfermedades.enfermedad_cronica_detalle'  => ['nullable', 'string', 'max:255'],
            'enfermedades.toma_medicamentos'           => ['nullable', 'boolean'],
            'enfermedades.toma_medicamentos_detalle'   => ['nullable', 'string', 'max:255'],
            'enfermedades.visita_medico'               => ['nullable', 'boolean'],
            'enfermedades.visita_medico_detalle'       => ['nullable', 'string', 'max:255'],
            'enfermedades.nombre_medico'               => ['nullable', 'string', 'max:100'],
            'enfermedades.telefono_medico'             => ['nullable', 'string', 'max:20'],

            'contacto.nombre'     => ['nullable', 'string', 'max:50'],
            'contacto.apellidos'  => ['nullable', 'string', 'max:50'],
            'contacto.domicilio'  => ['nullable', 'string', 'max:100'],
            'contacto.telefono'   => ['nullable', 'string', 'max:50'],
            'contacto.parentesco' => ['nullable', 'string', 'max:50'],
            'contacto.institucion' => ['required', \Illuminate\Validation\Rule::in(['IMSS', 'Cruz Roja', 'Privado'])],
        ]);

        DB::transaction(function () use ($data, $ficha) {
            $ficha->alergias()->update($data['alergias'] ?? []);
            $ficha->enfermedades()->update($data['enfermedades'] ?? []);
            $ficha->contacto()->update($data['contacto'] ?? []);
        });

        return redirect()->route('mi_ficha.show')->with('ok', 'Ficha médica actualizada.');
    }
}
