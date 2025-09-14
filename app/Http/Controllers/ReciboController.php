<?php

namespace App\Http\Controllers;

use App\Models\Recibo;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReciboController extends Controller
{
    // ===== Helpers de rol (sin tocar tu middleware) =====
    private function isAdminLike(): bool
    {
        $rol = auth()->user()->rol->nombre_rol ?? null;
        return in_array(strtolower($rol), ['administrador','coordinador','superadmin'], true);
    }

    private function currentAlumnoId(): ?int
    {
        // Si no tienes relación en el modelo Usuario->alumno, resuélvelo por consulta:
        return Alumno::where('id_usuario', auth()->id())->value('id_alumno');
        // Alternativa sin modelo:
        // return DB::table('alumnos')->where('id_usuario', auth()->id())->value('id_alumno');
    }

    // ===== Alumno =====
    public function indexAlumno(Request $request)
    {
        // Lista SOLO los del alumno autenticado
        $alumnoId = $this->currentAlumnoId();
        abort_unless($alumnoId, 403);

        $recibos = Recibo::with('alumno')
            ->where('id_alumno', $alumnoId)
            ->latest('id_recibo')
            ->paginate(15)
            ->withQueryString();

        return view('CRUDRecibo.read', compact('recibos')); // usa tu layout
    }

    public function create()
    {
        // Form para crear (el alumno NO ve id_alumno)
        return view('CRUDrecibo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_pago'  => ['required','date'],
            'concepto'    => ['required','string','max:100'],
            'monto'       => ['required','numeric','min:0'],
            'comprobante' => ['required','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'comentarios' => ['nullable','string'],
            // (Admin-only) Si algún día quieres permitir que el admin cree para otro alumno:
            // 'id_alumno' => ['sometimes','integer','exists:alumnos,id_alumno']
        ]);

        // Alumno: se toma de sesión; Admin podría pasar id_alumno (opcional)
        $alumnoId = $this->currentAlumnoId();
        if ($this->isAdminLike() && $request->filled('id_alumno')) {
            $alumnoId = (int) $request->input('id_alumno');
        }
        abort_unless($alumnoId, 403);

        $path = $request->file('comprobante')->store('recibos', 'public');

        $recibo = Recibo::create([
            'id_alumno'        => $alumnoId,
            'fecha_pago'       => $request->fecha_pago,
            'concepto'         => $request->concepto,
            'monto'            => $request->monto,
            'comprobante_path' => $path, // guardamos la ruta relativa
            'estatus'          => 'pendiente',
            'comentarios'      => $request->comentarios,
        ]);

        return redirect()->route('recibos.index', $recibo->id_recibo)->with('ok', 'Recibo registrado correctamente.');
    }

    public function show(Recibo $recibo)
    {
        if (!$this->isAdminLike()) {
            // Alumno sólo puede ver el suyo
            $alumnoId = $this->currentAlumnoId();
            abort_unless($alumnoId && $recibo->id_alumno === $alumnoId, 403);
        }
        return view('recibos.show', compact('recibo'));
    }

    // ===== Admin-like =====
    public function indexAdmin(Request $request)
    {
        $recibos = Recibo::with(['alumno','validador'])
            ->latest('id_recibo')
            ->paginate(15)
            ->withQueryString();

        return view('CRUDRecibo.index_admin', compact('recibos'));
    }

    public function edit(Recibo $recibo)
    {
        // Solo Admin-like (ya viene filtrado por middleware de ruta)
        return view('recibos.edit', compact('recibo'));
    }

    public function update(Request $request, Recibo $recibo)
    {
        $request->validate([
            'fecha_pago'  => ['required','date'],
            'concepto'    => ['required','string','max:100'],
            'monto'       => ['required','numeric','min:0'],
            'comprobante' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'estatus'     => ['required', Rule::in(['pendiente','validado','rechazado'])],
            'comentarios' => ['nullable','string'],
        ]);

        $data = $request->only(['fecha_pago','concepto','monto','estatus','comentarios']);

        if ($request->hasFile('comprobante')) {
            if ($recibo->comprobante_path && Storage::disk('public')->exists($recibo->comprobante_path)) {
                Storage::disk('public')->delete($recibo->comprobante_path);
            }
            $data['comprobante_path'] = $request->file('comprobante')->store('recibos', 'public');
        }

        if (in_array($data['estatus'], ['validado','rechazado'], true)) {
            $data['fecha_validacion'] = now();
            $data['validado_por']     = auth()->id();
        } else {
            $data['fecha_validacion'] = null;
            $data['validado_por']     = null;
        }

        $recibo->update($data);

        return redirect()->route('recibos.show', $recibo->id_recibo)->with('ok', 'Recibo actualizado.');
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
            'estatus'     => ['required', Rule::in(['validado','rechazado'])],
            'comentarios' => ['nullable','string'],
        ]);

        $recibo->update([
            'estatus'          => $request->estatus,
            'fecha_validacion' => now(),
            'validado_por'     => auth()->id(),
            'comentarios'      => $request->comentarios,
        ]);

        return back()->with('ok', 'Recibo validado/actualizado.');
    }
}
