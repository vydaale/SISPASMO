<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Alumno;
use App\Models\Modulo;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Horario;

class CalificacionController extends Controller
{
    private function rol(): ?string
    {
        return strtolower(auth()->user()->rol->nombre_rol ?? '');
    }

    private function isAdminLike(): bool
    {
        return in_array($this->rol(), ['administrador', 'coordinador', 'superadmin'], true);
    }

    private function currentAlumnoId(): ?int
    {
        return Alumno::where('id_usuario', auth()->id())->value('id_alumno');
    }

    private function currentDocenteId(): ?int
    {
        return Docente::where('id_usuario', auth()->id())->value('id_docente');
    }

    public function indexAlumno(Request $request)
    {
        $alumnoId = $this->currentAlumnoId();
        abort_unless($alumnoId, 403);

        $q = Calificacion::with(['modulo', 'docente.usuario'])
            ->where('id_alumno', $alumnoId);

        if ($m = $request->id_modulo) $q->where('id_modulo', $m);
        if ($t = $request->tipo)      $q->where('tipo', $t);

        $califs = $q->orderByDesc('id_calif')->paginate(15)->withQueryString();
        $modulos = Modulo::orderBy('numero_modulo')->get(['id_modulo', 'nombre_modulo', 'numero_modulo']);

        return view('CRUDCalificaciones.read_alumno', compact('califs', 'modulos'));
    }

    public function indexDocente(Request $request)
    {
        $docenteId = $this->currentDocenteId();
        abort_unless($docenteId, 403);

        $q = Calificacion::with(['alumno.usuario', 'modulo'])
            ->where('id_docente', $docenteId);

        if ($a = $request->id_alumno) $q->where('id_alumno', $a);
        if ($m = $request->id_modulo) $q->where('id_modulo', $m);
        if ($t = $request->tipo)      $q->where('tipo', $t);

        $califs = $q->orderByDesc('id_calif')->paginate(15)->withQueryString();

        $misAlumnos = Alumno::with('diplomado')->orderBy('id_alumno')->get(['id_alumno', 'id_diplomado', 'id_usuario']);
        $modulos    = Modulo::orderBy('numero_modulo')->get(['id_modulo', 'nombre_modulo', 'numero_modulo']);

        return view('CRUDCalificaciones.read', compact('califs', 'misAlumnos', 'modulos'));
    }

    public function create()
    {
        $docenteId = $this->currentDocenteId();
        abort_unless($docenteId, 403);

        // 1. Obtener los IDs de los módulos únicos que este docente tiene en la tabla de horarios.
        $modulosIds = Horario::where('id_docente', $docenteId)
            ->distinct()
            ->pluck('id_modulo');

        // 2. Obtener los modelos completos de esos módulos.
        $modulos = Modulo::whereIn('id_modulo', $modulosIds)
            ->orderBy('numero_modulo')
            ->get(['id_modulo', 'nombre_modulo', 'numero_modulo']);

        // 3. Pasamos solo los módulos a la vista. Los alumnos se cargarán con JS.
        return view('CRUDCalificaciones.create', compact('modulos'));
    }

    public function getAlumnosPorModulo(Modulo $modulo)
    {
        $docenteId = $this->currentDocenteId();
        abort_unless($docenteId, 403);

        // 1. Obtener los diplomados asociados a este docente y módulo.
        $diplomadosIds = Horario::where('id_docente', $docenteId)
            ->where('id_modulo', $modulo->id_modulo)
            ->distinct()
            ->pluck('id_diplomado');

        // 2. Obtener los alumnos 'activos' de esos diplomados.
        $alumnos = Alumno::with('usuario:id_usuario,nombre,apellidoP,apellidoM')
            ->whereIn('id_diplomado', $diplomadosIds)
            ->where('estatus', 'activo')
            ->get();

        // 3. Formatear para la respuesta JSON.
        $alumnosData = $alumnos->map(function ($alumno) {
            $nombreCompleto = trim(optional($alumno->usuario)->nombre . ' ' . optional($alumno->usuario)->apellidoP . ' ' . optional($alumno->usuario)->apellidoM);
            return [
                'id' => $alumno->id_alumno,
                'nombre' => $nombreCompleto ?: 'Alumno #' . $alumno->id_alumno,
            ];
        })->sortBy('nombre');

        return response()->json($alumnosData);
    }

    public function store(Request $request)
    {
        $docenteId = $this->currentDocenteId();
        abort_unless($docenteId, 403);
    
        $request->validate([
            'id_alumno' => ['required', 'integer', 'exists:alumnos,id_alumno'],
            'id_modulo' => ['required', 'integer', 'exists:modulos,id_modulo'],
            'tipo' => ['required', 'string', 'max:50'],
            'observacion' => ['nullable', 'string'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
    
        // VALIDACIÓN DE PERMISO: Verificar que el docente puede calificar a este alumno en este módulo.
        $idDiplomadoAlumno = Alumno::find($request->id_alumno)->id_diplomado;
        $tienePermiso = Horario::where('id_docente', $docenteId)
            ->where('id_modulo', $request->id_modulo)
            ->where('id_diplomado', $idDiplomadoAlumno)
            ->exists();
    
        if (!$tienePermiso) {
            return back()->withErrors(['id_alumno' => 'No tienes permiso para calificar a este alumno en el módulo seleccionado.'])->withInput();
        }
    
        // VALIDACIÓN DE DUPLICADO: (Tu regla original es compleja, esta es una alternativa más simple)
        $duplicado = Calificacion::where('id_alumno', $request->id_alumno)
            ->where('id_modulo', $request->id_modulo)
            ->where('tipo', $request->tipo)
            ->exists();

        if($duplicado) {
             return back()->withErrors(['tipo' => 'Ya existe una calificación de este tipo para el alumno en este módulo.'])->withInput();
        }
    
        Calificacion::create([
            'id_alumno' => $request->id_alumno,
            'id_modulo' => $request->id_modulo,
            'id_docente' => $docenteId,
            'tipo' => $request->tipo,
            'observacion' => $request->observacion,
            'calificacion' => $request->calificacion,
        ]);
    
        return redirect()->route('calif.docente.index')->with('ok', 'Calificación registrada.');
    }

    public function edit(Calificacion $calif)
    {
        $docenteId = $this->currentDocenteId();
        abort_unless($docenteId && $calif->id_docente === $docenteId, 403);

        $alumnos = Alumno::with('usuario', 'diplomado')->orderBy('id_alumno')->get();
        $modulos = Modulo::orderBy('numero_modulo')->get(['id_modulo', 'nombre_modulo', 'numero_modulo']);

        return view('CRUDCalificaciones.update', compact('calif', 'alumnos', 'modulos'));
    }

    public function update(Request $request, Calificacion $calif)
    {
        $docenteId = $this->currentDocenteId();
        abort_unless($docenteId && $calif->id_docente === $docenteId, 403);

        $request->validate([
            'id_alumno'    => ['required', 'integer', 'exists:alumnos,id_alumno'],
            'id_modulo'    => ['required', 'integer', 'exists:modulos,id_modulo'],
            'tipo'         => ['required', 'string', 'max:50'],
            'observacion'  => ['nullable', 'string'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:100'],
            Rule::unique('calificaciones', 'id_calif')->where(function ($q) use ($request, $docenteId, $calif) {
                $q->where('id_alumno', $request->id_alumno)
                    ->where('id_modulo', $request->id_modulo)
                    ->where('tipo', $request->tipo)
                    ->where('id_docente', $docenteId);
            })->ignore($calif->id_calif, 'id_calif')
        ]);

        $calif->update($request->only('id_alumno', 'id_modulo', 'tipo', 'observacion', 'calificacion'));

        return redirect()->route('calif.docente.index')->with('ok', 'Calificación actualizada.');
    }

    public function destroy(Calificacion $calif)
    {
        $docenteId = $this->currentDocenteId();
        abort_unless($docenteId && $calif->id_docente === $docenteId, 403);

        $calif->delete();
        return redirect()->route('calif.docente.index')->with('ok', 'Calificación eliminada.');
    }

    public function indexAdmin(Request $request)
    {
        $q = Calificacion::with(['alumno.usuario', 'docente.usuario', 'modulo']);

        if ($a = $request->id_alumno) $q->where('id_alumno', $a);
        if ($m = $request->id_modulo) $q->where('id_modulo', $m);
        if ($t = $request->tipo)      $q->where('tipo', $t);

        $califs  = $q->orderByDesc('id_calif')->paginate(20)->withQueryString();
        $modulos = Modulo::orderBy('numero_modulo')->get(['id_modulo', 'nombre_modulo', 'numero_modulo']);
        $alumnos = Alumno::orderBy('id_alumno')->get(['id_alumno', 'grupo', 'num_diplomado', 'id_usuario']);

        return view('calificaciones.admin.index', compact('califs', 'modulos', 'alumnos'));
    }
}
