<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // === Layout por rol ===
        $layout = $this->layoutFor($user); // 'layouts.encabezados' o 'layouts.encabezadosAl'

        // Filtro y paginación (opcional, como te propuse antes)
        $estado = $request->get('estado', 'all'); // all|unread|read
        $query  = $user->notifications()->latest();

        if ($estado === 'unread') {
            $query->whereNull('read_at');
        } elseif ($estado === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(15)->withQueryString();

        // Marcar como leídas solo las visibles (opcional)
        $idsVisiblesNoLeidas = $notifications->getCollection()
            ->whereNull('read_at')
            ->pluck('id');

        if ($idsVisiblesNoLeidas->isNotEmpty()) {
            $user->notifications()
                ->whereIn('id', $idsVisiblesNoLeidas)
                ->update(['read_at' => now()]);

            $notifications->getCollection()->transform(function ($n) {
                $n->read_at = now();
                return $n;
            });
        }

        return view('notificaciones.index', compact('notifications', 'estado', 'layout'));
    }

    private function layoutFor($user): string
    {
        $rol = optional($user->rol)->nombre_rol;
        $isAdminLike = in_array(strtolower((string)$rol), ['administrador', 'coordinador', 'superadmin'], true);

        return $isAdminLike ? 'layouts.encabezados' : 'layouts.encabezadosAl';
    }

    public function markOne($id)
    {
        $n = Auth::user()->notifications()->findOrFail($id);
        if (is_null($n->read_at)) $n->markAsRead();
        return back();
    }

    public function markAll()
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        return back();
    }

    public function destroy($id)
    {
        Auth::user()->notifications()->where('id', $id)->delete();
        return back();
    }
}
