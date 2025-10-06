<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Horario;
use App\Models\Modulo;
use App\Models\Diplomado;
use App\Models\Docente;
use App\Models\Alumno;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Notifications\HorarioClaseNotification;


class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::with([
            'diplomado',
            'modulo',
            'docente.usuario'
        ])->orderBy('fecha')->orderBy('hora_inicio')->paginate(10);

        return view('CRUDHorarios.index', compact('horarios'));
    }

    public function create()
    {
        $diplomados = Diplomado::all();
        $modulos = Modulo::orderBy('nombre_modulo')->get(['id_modulo', 'nombre_modulo']);
        $docentes = Docente::all();

        return view('CRUDHorarios.create', compact('diplomados', 'modulos', 'docentes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'modalidad' => ['required', Rule::in(['Presencial', 'Virtual', 'Práctica'])],
            'aula' => ['required', 'string', 'max:20'],
            'id_diplomado' => ['required', 'integer', 'exists:diplomados,id_diplomado'],
            'id_modulo' => ['required', 'integer', 'exists:modulos,id_modulo'],
            'id_docente' => ['required', 'integer', 'exists:docentes,id_docente'],
        ]);

        $conflict = Horario::where('id_docente', $data['id_docente'])
            ->where('fecha', $data['fecha'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('hora_inicio', [$data['hora_inicio'], $data['hora_fin']])
                    ->orWhereBetween('hora_fin', [$data['hora_inicio'], $data['hora_fin']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('hora_inicio', '<=', $data['hora_inicio'])
                            ->where('hora_fin', '>=', $data['hora_fin']);
                    });
            })->first();

        if ($conflict) {
            return back()->withErrors(['docente' => 'El docente ya tiene un horario asignado en ese día y rango de horas.'])->withInput();
        }

        DB::transaction(function () use ($data) {
            
            $horario = Horario::create($data);
            
            $observacion = '¡Nueva clase programada! Revisa los detalles de este nuevo horario.';
            
            $alumnos = Alumno::where('id_diplomado', $horario->id_diplomado)
                                ->whereHas('usuario')
                                ->get();

            foreach ($alumnos as $alumno) {
                if ($alumno->usuario) { 
                    $alumno->usuario->notify(new HorarioClaseNotification($horario, $observacion)); 
                }
            }
        });
        
        return redirect()->route('admin.horarios.index')->with('success', 'Horario creado exitosamente. Se ha enviado la notificación a los alumnos.');
    }

    public function edit(Horario $horario)
    {
        $diplomados = Diplomado::all();
        $modulos = Modulo::all();
        $docentes = Docente::all();

        return view('CRUDHorarios.update', compact('horario', 'diplomados', 'modulos', 'docentes'));
    }
    
    public function update(Request $request, Horario $horario)
    {
        $data = $request->validate([
            'fecha' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'modalidad' => ['required', Rule::in(['Presencial', 'Virtual', 'Práctica'])],
            'aula' => ['required', 'string', 'max:20'],
            'id_diplomado' => ['required', 'integer', 'exists:diplomados,id_diplomado'],
            'id_modulo' => ['required', 'integer', 'exists:modulos,id_modulo'],
            'id_docente' => ['required', 'integer', 'exists:docentes,id_docente'],
        ]);

        $conflict = Horario::where('id_docente', $data['id_docente'])
            ->where('fecha', $data['fecha'])
            ->where('id_horario', '!=', $horario->id_horario)
            ->where(function ($query) use ($data) {
                $query->whereBetween('hora_inicio', [$data['hora_inicio'], $data['hora_fin']])
                    ->orWhereBetween('hora_fin', [$data['hora_inicio'], $data['hora_fin']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('hora_inicio', '<=', $data['hora_inicio'])
                            ->where('hora_fin', '>=', $data['hora_fin']);
                    });
            })->first();

        if ($conflict) {
            return back()->withErrors(['docente' => 'El docente ya tiene un horario asignado en ese día y rango de horas.'])->withInput();
        }
        
        $cambio_relevante = $horario->isDirty(['fecha', 'hora_inicio', 'hora_fin', 'modalidad', 'aula', 'id_modulo', 'id_docente']);

        DB::transaction(function () use ($data, $horario, $cambio_relevante) {
            $horario->update($data);
            
            if ($cambio_relevante) {
                
                $observacion = '¡Clase Actualizada! Se ha modificado tu clase. Revisa los nuevos detalles.';

                $alumnos = Alumno::where('id_diplomado', $horario->id_diplomado)
                                    ->whereHas('usuario')
                                    ->get();
                                    
                foreach ($alumnos as $alumno) {
                    if ($alumno->usuario) {
                        $alumno->usuario->notify(new HorarioClaseNotification($horario, $observacion));
                    }
                }
            }
        });

        return redirect()->route('admin.horarios.index')->with('success', 'Horario actualizado exitosamente. Se ha notificado a los alumnos sobre el cambio.');
    }

    public function destroy(Horario $horario)
    {
        DB::transaction(function () use ($horario) {
            $horario->delete();
        });
        
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado exitosamente.');
    }
    
    public function horarioAlumno()
    {
        $user = Auth::user();
        $alumno = optional($user)->alumno;
        abort_if(!$alumno, 403, 'No se encontró el perfil de alumno.');

        $query = Horario::with(['diplomado','modulo','docente.usuario'])
            ->whereDate('fecha', '>=', now()->toDateString());

        if (isset($alumno->id_grupo)) {
            $query->where('id_grupo', $alumno->id_grupo);
        }

        if (isset($alumno->id_diplomado)) {
            $query->where('id_diplomado', $alumno->id_diplomado);
        }

        $horarios = $query->orderBy('fecha')->orderBy('hora_inicio')->get();

        return view('CRUDHorarios.indexAl', compact('horarios'));
    }

    public function horarioDocente()
    {
        $user = Auth::user();
        $docente = optional($user)->docente; 
        abort_if(!$docente, 403, 'No se encontró el perfil de docente.');

        $horarios = Horario::with(['diplomado','modulo','docente.usuario'])
            ->where('id_docente', $docente->id_docente)
            ->whereDate('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->get();

        return view('CRUDHorarios.indexDoc', compact('horarios'));
    }
}