<?php

namespace App\Http\Controllers;

use App\Models\Queja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class QuejaController extends Controller
{
    /* ====== USUARIO AUTENTICADO (Docente/Alumno/Aspirante/Coordinador/Admin) ====== */

    // Muestra el formulario para crear una nueva queja o sugerencia
    public function create()
    {
        return view('quejas.create');
    }

    // Almacena una nueva queja o sugerencia
    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo'     => ['required', 'in:queja,sugerencia'],
            'mensaje'  => ['required', 'string', 'min:10', 'max:5000'],
            'contacto' => ['nullable', 'string', 'max:100'],
        ], [
            'tipo.required' => 'Selecciona si es queja o sugerencia.',
            'mensaje.min'   => 'Cuéntanos con un poco más de detalle (mínimo 10 caracteres).',
        ]);

        $data['id_usuario'] = Auth::id();
        $data['estatus']    = 'Pendiente';

        Queja::create($data);

        return redirect()->route('quejas.propias')
            ->with('ok', "¡Gracias! Recibimos tu {$data['tipo']}.");
    }

    // Muestra las quejas del usuario autenticado
    public function mine()
    {
        $quejas = Queja::where('id_usuario', Auth::id())
            ->orderByDesc('id_queja')
            ->paginate(10);

        return view('quejas.quejaspropias', compact('quejas'));
    }

    // Muestra los detalles de una queja específica
    public function show(Queja $queja)
    {
        if (!$this->isAllowedToView($queja)) {
            abort(403, 'No tienes permiso para ver esta queja.');
        }

        $queja->load('usuario');
        return view('administrador.CRUDQuejas.read', compact('queja'));
    }

    /* ====== ADMIN ====== */

    // Muestra una lista filtrable de todas las quejas para administradores
    public function index(Request $request)
    {
        $query = Queja::with('usuario')->orderByDesc('id_queja');

        // Aplicar filtros
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('q')) {
            $term = "%{$request->q}%";
            $query->where(function($q) use ($term) {
                $q->where('mensaje', 'like', $term)
                    ->orWhere('contacto', 'like', $term)
                    ->orWhereHas('usuario', function($userQuery) use ($term) {
                        $userQuery->where('nombre', 'like', $term)
                            ->orWhere('apellidoP', 'like', $term)
                            ->orWhere('apellidoM', 'like', $term)
                            ->orWhere('usuario', 'like', $term)
                            ->orWhere('correo', 'like', $term);
                    });
            });
        }

        $quejas = $query->paginate(15)->appends($request->query());

        return view('administrador.CRUDQuejas.read', compact('quejas'));
    }

    // Muestra el formulario para editar una queja
    public function edit(Queja $queja)
    {
        return view('administrador.CRUDQuejas.update', compact('queja'));
    }

    // Actualiza una queja existente
    public function update(Request $request, Queja $queja)
    {
        $data = $request->validate([
            'estatus'  => ['required', Rule::in(['Pendiente', 'Atendido'])],
            'contacto' => ['nullable', 'string', 'max:100'],
            'mensaje'  => ['nullable', 'string', 'min:10', 'max:5000'],
        ], [
            'estatus.in' => 'estatus inválido.',
        ]);

        $queja->update($data);

        return redirect()->route('quejas.index')->with('success', 'Actualizado.');
    }

    // Elimina una queja
    public function destroy(Queja $queja)
    {
        $queja->delete();
        return redirect()->route('quejas.index')->with('success', 'Eliminado.');
    }

    /**
     * Revisa si el usuario actual tiene permiso para ver la queja.
     *
     * @param Queja $queja
     * @return bool
     */
    protected function isAllowedToView(Queja $queja): bool
    {
        $user = Auth::user();
        $isOwner = $queja->id_usuario === $user->id;
        $isAdmin = $user?->rol?->nombre_rol === 'Administrador';

        return $isOwner || $isAdmin;
    }
}