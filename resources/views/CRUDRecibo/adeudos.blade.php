@extends('layouts.encabezados')
@section('title', 'Alumnos con adeudos')

@push('styles')
<style>
    .btn-notificar {
        background-color: #f5f6fa;
        border: 1px solid #dcdde1;
        color: #2f3640;
        padding: 10px 20px;
        font-size: 0.95rem;
        font-weight: 500;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-notificar:hover {
        background-color: #e1e3e8;
        border-color: #c5c7cc;
    }
    .btn-notificar:active {
        background-color: #d8dadf;
    }
    .btn-notificar i {
        font-size: 1.1rem;
        color: #57606f;
        transition: color 0.25s ease;
    }
    .btn-notificar:hover i {
        color: #2f3640;
    }
</style>
@endpush

@section('content')
<div class="crud-wrap">
    <section class="crud-card">
        <header class="crud-hero">
            <h2 class="crud-hero-title">Alumnos con adeudos</h2>
            <p class="crud-hero-subtitle">Listado de alumnos sin recibos del mes</p>
        </header>

        <div class="crud-body">
            @if (session('ok'))
                <div class="gm-ok">{{ session('ok') }}</div>
            @endif

            <form action="{{ route('recibos.notificar') }}" method="POST">
                @csrf
                <button type="submit" class="btn-notificar">
                    <i class="fa-solid fa-bell"></i>
                    Notificar adeudos
                </button>
            </form>

            <div class="table-responsive" style="margin-top: 20px;">
                <table class="gm-table">
                    <thead>
                        <tr>
                            <th>Nombre completo</th>
                            <th>Matrícula</th>
                            <th>Correo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alumnosConAdeudos as $a)
                            <tr>
                                <td>{{ $a->usuario->nombre ?? '—' }} {{ $a->usuario->apellidoP ?? '' }} {{ $a->usuario->apellidoM ?? '' }}</td>
                                <td>{{ $a->usuario->usuario }}</td>
                                <td>{{ $a->usuario->correo ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3">No hay alumnos con adeudos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
