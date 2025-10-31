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
    /*
     * Muestra una lista paginada de todos los Horarios (Uso administrativo/Coordinador). Carga las relaciones 
        de diplomado, módulo y docente/usuario para mostrar la información completa y los ordena por fecha y hora de 
        inicio.
    */
    public function index(Request $request)
{
    // Obtener listas para los filtros de la vista
    $docentes = Docente::with('usuario')->get();
    $diplomados = Diplomado::all(); // 👈 NUEVO: Cargar todos los Diplomados

    // Iniciar la consulta base con las relaciones necesarias y ordenamiento.
    $query = Horario::with([
        'diplomado',
        'modulo',
        'docente.usuario'
    ])->orderBy('fecha')->orderBy('hora_inicio');

    // 1. Aplicar filtro por DIPLOMADO
    if ($request->filled('id_diplomado')) {
        $query->where('id_diplomado', $request->input('id_diplomado'));
    }
    
    // 2. Aplicar filtro por DOCENTE
    if ($request->filled('id_docente')) {
        $query->where('id_docente', $request->input('id_docente'));
    }

    // 3. Aplicar filtro por FECHA
    // Se filtra por la fecha exacta
    if ($request->filled('fecha')) {
        $query->whereDate('fecha', $request->input('fecha'));
    }

    // 4. Aplicar filtro por AULA
    if ($request->filled('aula')) {
        $query->where('aula', 'like', '%' . $request->input('aula') . '%');
    }

    // Ejecutar la consulta con la paginación.
    $horarios = $query->paginate(10)->withQueryString(); 
    // `withQueryString()` mantiene los filtros en la paginación.

    // Pasar también los parámetros de filtro actuales para mantener los valores en el formulario.
    $filtros = $request->only(['id_docente', 'fecha', 'aula', 'id_diplomado']); // 👈 NUEVO: Incluir id_diplomado

    // 👈 NUEVO: Pasar la colección de diplomados a la vista
    return view('CRUDHorarios.index', compact('horarios', 'docentes', 'filtros', 'diplomados'));
}


    /*
     * Muestra el formulario para crear un nuevo Horario. Carga el listado de Diplomados, Módulos y Docentes 
        para las opciones del formulario.
    */
    public function create()
    {
        $diplomados = Diplomado::all();
        $modulos = Modulo::orderBy('nombre_modulo')->get(['id_modulo', 'nombre_modulo']);
        $docentes = Docente::all();

        return view('CRUDHorarios.create', compact('diplomados', 'modulos', 'docentes'));
    }


    /*
     * Almacena un nuevo Horario y notifica a los alumnos del diplomado.
    */
    public function store(Request $request)
    {
        /* Valida los datos, asegurando la hora de fin sea posterior a la de inicio. */
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

        /* Verifica que el docente no tenga ya un horario asignado que se solape en ese día y rango. */
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

        /* Ejecuta una transacción: crea el horario y luego notifica a todos los alumnos
            inscritos en el diplomado asociado. */
        DB::transaction(function () use ($data) {
            
            $horario = Horario::create($data);
            $observacion = '¡Nueva clase programada! Revisa los detalles de este nuevo horario.';
            $alumnos = Alumno::where('id_diplomado', $horario->id_diplomado) ->whereHas('usuario')->get();

            foreach ($alumnos as $alumno) {
                if ($alumno->usuario) { 
                    $alumno->usuario->notify(new HorarioClaseNotification($horario, $observacion)); 
                }
            }
        });
        
        return redirect()->route('admin.horarios.index')->with('success', 'Horario creado exitosamente. Se ha enviado la notificación a los alumnos.');
    }


    /*
     * Muestra el formulario para editar un Horario existente. Carga la instancia del horario y los listados completos de diplomados, módulos y docentes.
    */
    public function edit(Horario $horario)
    {
        $diplomados = Diplomado::all();
        $modulos = Modulo::all();
        $docentes = Docente::all();

        return view('CRUDHorarios.update', compact('horario', 'diplomados', 'modulos', 'docentes'));
    }
    

    /*
     * Actualiza un Horario existente y notifica a los alumnos si hubo cambios relevantes.
    */
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

        /* Verifica que la actualización no cause un solapamiento con otros horarios del mismo docente. */
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
        
        /* Detecta si hubo cambios en los campos clave (`isDirty`). */
        $cambio_relevante = $horario->isDirty(['fecha', 'hora_inicio', 'hora_fin', 'modalidad', 'aula', 'id_modulo', 'id_docente']);

        /* Ejecuta una transacción: actualiza el horario y, si hubo cambios, notifica a los alumnos. */
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


    /*
     * Elimina un Horario de la base de datos. Ejecuta la eliminación dentro de una transacción.
    */
    public function destroy(Horario $horario)
    {
        DB::transaction(function () use ($horario) {
            $horario->delete();
        });
        
        return redirect()->route('admin.horarios.index')->with('success', 'Horario eliminado exitosamente.');
    }
    

    /*
     * Muestra el horario de clases para el Alumno autenticado. Filtra los horarios futuros basados en el 
        `id_diplomado` (y opcionalmente `id_grupo`) del alumno.
    */
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


    /*
     * Muestra el horario de clases para el Docente autenticado. Filtra los horarios futuros donde el 
        `id_docente` coincide con el docente autenticado.
    */
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