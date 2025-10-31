<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use App\Models\Alumno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ReciboController extends Controller
{
    private function isAdminLike(): bool
    {
        $rol = auth()->user()->rol->nombre_rol ?? null;
        return in_array(strtolower($rol), ['administrador', 'coordinador', 'superadmin'], true);
    }

    private function currentAlumnoId(): ?int
    {
        return Alumno::where('id_usuario', auth()->id())->value('id_alumno');
    }

    public function indexAlumno(Request $request)
    {
        $alumnoId = $this->currentAlumnoId();
        abort_unless($alumnoId, 403);

        $recibos = Recibo::with('alumno')
            ->where('id_alumno', $alumnoId)
            ->latest('id_recibo')
            ->paginate(15)
            ->withQueryString();

        return view('CRUDRecibo.read', compact('recibos'));
    }

    public function create()
    {
        $alumno = Alumno::with('diplomado')->where('id_usuario', auth()->id())->first();

        abort_unless($alumno && $alumno->diplomado, 403);

        $conceptos = $this->generarConceptosDePago($alumno->diplomado->fecha_inicio);

        return view('CRUDrecibo.create', compact('conceptos', 'alumno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_pago'  => ['required', 'date'],
            'concepto'    => ['required', 'string', 'max:100'],
            'monto'       => ['required', 'numeric', 'min:0'],
            'matriculaA'   => [
                'required',
                'string',
                'exists:alumnos,matriculaA'
            ],
            'comprobante' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120'
            ],
            'comentarios' => ['nullable', 'string'],
        ]);

        $alumno = Alumno::where('matriculaA', $request->matriculaA)->first();

        if (!$alumno || $alumno->id_usuario !== auth()->id()) {
            return back()->withErrors(['matriculaA' => 'La matrícula no coincide con tu perfil de usuario.'])->withInput();
        }

        $path = $request->file('comprobante')->store('recibos', 'public');

        Recibo::create([
            'id_alumno'        => $alumno->id_alumno,
            'fecha_pago'       => $request->fecha_pago,
            'concepto'         => $request->concepto,
            'monto'            => $request->monto,
            'comprobante_path' => $path,
            'estatus'          => 'pendiente',
            'comentarios'      => $request->comentarios,
        ]);

        return redirect()->route('recibos.index')->with('ok', 'Recibo registrado correctamente.');
    }

    public function show(Recibo $recibo)
    {
        if (!$this->isAdminLike()) {
            $alumnoId = $this->currentAlumnoId();
            abort_unless($alumnoId && $recibo->id_alumno === $alumnoId, 403);
        }
        return view('recibos.show', compact('recibo'));
    }

    public function indexAdmin(Request $request)
    {
        $query = Recibo::with(['alumno', 'validador'])->latest('id_recibo');

        if ($q = $request->q) {
            $query->where('concepto', 'like', "%{$q}%")
                ->orWhereHas('alumno', function ($q_al) use ($q) {
                    $q_al->where('nombre', 'like', "%{$q}%")
                        ->orWhere('matriculaA', 'like', "%{$q}%");
                });
        }

        if ($estatus = $request->estatus) {
            $query->where('estatus', $estatus);
        }

        if ($f1 = $request->f1) {
            $query->whereDate('fecha_pago', '>=', $f1);
        }

        if ($f2 = $request->f2) {
            $query->whereDate('fecha_pago', '<=', $f2);
        }

        $recibos = $query->paginate(15)->withQueryString();

        return view('CRUDRecibo.index_admin', compact('recibos'));
    }

    public function edit(Recibo $recibo)
    {
        return view('recibos.edit', compact('recibo'));
    }

    public function update(Request $request, Recibo $recibo)
    {
        $request->validate([
            'fecha_pago'  => ['required', 'date'],
            'concepto'    => ['required', 'string', 'max:100'],
            'monto'       => ['required', 'numeric', 'min:0'],
            'comprobante' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'estatus'     => ['required', Rule::in(['pendiente', 'validado', 'rechazado'])],
            'comentarios' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['fecha_pago', 'concepto', 'monto', 'estatus', 'comentarios']);

            if ($request->hasFile('comprobante')) {
                if ($recibo->comprobante_path && Storage::disk('public')->exists($recibo->comprobante_path)) {
                    Storage::disk('public')->delete($recibo->comprobante_path);
                }
                $data['comprobante_path'] = $request->file('comprobante')->store('recibos', 'public');
            }

            if (in_array($data['estatus'], ['validado', 'rechazado'], true)) {
                $data['fecha_validacion'] = now();
                $data['validado_por']     = auth()->id();
            } else {
                $data['fecha_validacion'] = null;
                $data['validado_por']     = null;
            }

            $recibo->update($data);

            $recibo->refresh()->load(['alumno','validador']);

            if ($recibo->estatus === 'validado') {
                $pdf = Pdf::loadView('CRUDRecibo.recibos', ['recibo' => $recibo]);

                $dir = 'recibos_pdf';
                if (!Storage::disk('public')->exists($dir)) {
                    Storage::disk('public')->makeDirectory($dir);
                }
                $filename = 'recibo_'.$recibo->id_recibo.'_'.Str::random(6).'.pdf';

                if ($recibo->pdf_path && Storage::disk('public')->exists($recibo->pdf_path)) {
                    Storage::disk('public')->delete($recibo->pdf_path);
                }

                Storage::disk('public')->put($dir.'/'.$filename, $pdf->output());
                $recibo->update(['pdf_path' => $dir.'/'.$filename]);

                $conceptoFinal = $recibo->concepto; // ya actualizado
                $cargo = \App\Models\Cargo::where('id_alumno', $recibo->id_alumno)
                    ->where('estatus', 'pendiente')
                    ->whereDate('fecha_limite', '<=', now()->addDays(7))
                    ->where('concepto', $conceptoFinal)
                    ->orderBy('fecha_limite')
                    ->first();

                if ($cargo) {
                    $cargo->update(['estatus' => 'pagado', 'id_recibo' => $recibo->id_recibo]);
                }
            } else {
                if ($recibo->pdf_path && Storage::disk('public')->exists($recibo->pdf_path)) {
                    Storage::disk('public')->delete($recibo->pdf_path);
                }
                $recibo->update(['pdf_path' => null]);
            }

            DB::commit();
            return redirect()->route('recibos.show', $recibo->id_recibo)->with('ok', 'Recibo actualizado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'No se pudo actualizar el recibo. Inténtalo de nuevo.'])->withInput();
        }
    }


    public function destroy(Recibo $recibo)
    {
        if ($recibo->comprobante_path && Storage::disk('public')->exists($recibo->comprobante_path)) {
            Storage::disk('public')->delete($recibo->comprobante_path);
        }
        $recibo->delete();

        return redirect()->route('recibos.admin.index')->with('ok', 'Recibo eliminado.');
    }

    public function validar(Request $request, Recibo $recibo)
    {
        $request->validate([
            'estatus'     => ['required', Rule::in(['validado', 'rechazado'])],
            'comentarios' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $recibo->update([
                'estatus'          => $request->estatus,
                'fecha_validacion' => now(),
                'validado_por'     => auth()->id(),
                'comentarios'      => $request->comentarios,
            ]);

            if ($recibo->estatus === 'validado') {
                $recibo->load(['alumno', 'validador']);

                $pdf = Pdf::loadView('CRUDRecibo.recibos', ['recibo' => $recibo]);

                $dir = 'recibos_pdf';
                if (!Storage::disk('public')->exists($dir)) {
                    Storage::disk('public')->makeDirectory($dir);
                }
                $filename = 'recibo_'.$recibo->id_recibo.'_'.Str::random(6).'.pdf';

                if ($recibo->pdf_path && Storage::disk('public')->exists($recibo->pdf_path)) {
                    Storage::disk('public')->delete($recibo->pdf_path);
                }

                Storage::disk('public')->put($dir.'/'.$filename, $pdf->output());
                $recibo->update(['pdf_path' => $dir.'/'.$filename]);

                $cargo = \App\Models\Cargo::where('id_alumno', $recibo->id_alumno)
                    ->where('estatus', 'pendiente')
                    ->whereDate('fecha_limite', '<=', now()->addDays(7))
                    ->where('concepto', $recibo->concepto)
                    ->orderBy('fecha_limite')
                    ->first();

                if ($cargo) {
                    $cargo->update(['estatus' => 'pagado', 'id_recibo' => $recibo->id_recibo]);
                }
            } else {
                if ($recibo->pdf_path && Storage::disk('public')->exists($recibo->pdf_path)) {
                    Storage::disk('public')->delete($recibo->pdf_path);
                }
                $recibo->update(['pdf_path' => null]);
            }

            DB::commit();
            return back()->with('ok', 'Recibo validado/actualizado.');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'No se pudo completar la validación. Inténtalo de nuevo.']);
        }
    }
    private function generarConceptosDePago(string $fechaInicioDiplomado): array
    {
        $conceptos = ['Inscripción'];
        $fecha = Carbon::parse($fechaInicioDiplomado);

        for ($i = 0; $i < 12; $i++) {
            $mesNombre = $fecha->locale('es')->monthName;
            $year = $fecha->year;
            $conceptos[] = 'Colegiatura ' . ucfirst($mesNombre) . ' ' . $year;
            $fecha->addMonth();
        }

        $conceptos[] = 'Graduación';

        return $conceptos;
    }
}