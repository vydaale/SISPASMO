<?php

namespace App\Exports;

use App\Models\Alumno;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AdeudosExport implements FromCollection, WithHeadings, WithMapping
{
    private $mes;
    private $anio;
    private $matricula;
    private $tipo;

    public function __construct($mes, $anio, $matricula = null, $tipo = 'mes')
    {
        $this->mes       = (int) $mes;
        $this->anio      = (int) $anio;
        $this->matricula = $matricula;
        $this->tipo      = $tipo;
    }

    public function collection()
    {
        $mesNombre = Carbon::createFromDate($this->anio, $this->mes, 1)
            ->locale('es')
            ->monthName;

        $concepto_adeudo = 'Colegiatura ' . ucfirst($mesNombre) . ' ' . $this->anio;

        $query = Alumno::with(['diplomado', 'usuario'])
            ->whereDoesntHave('recibos', function ($q) use ($concepto_adeudo) {
                $q->where('concepto', $concepto_adeudo)
                  ->where('estatus', 'validado');
            });

        if ($this->tipo === 'alumno' && !empty($this->matricula)) {
            $query->where('matriculaA', $this->matricula);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Matricula',
            'Nombre del Alumno',
            'Concepto de Adeudo',
            'Grupo',
            'Mes y Año del Adeudo',
        ];
    }

    public function map($alumno): array
    {
        $mesNombre = Carbon::createFromDate($this->anio, $this->mes, 1)
            ->locale('es')
            ->monthName;

        $nombreCompleto = trim(
            ($alumno->usuario->nombre    ?? '') . ' ' .
            ($alumno->usuario->apellidoP ?? '') . ' ' .
            ($alumno->usuario->apellidoM ?? '')
        );

        $concepto_adeudo = 'Colegiatura ' . ucfirst($mesNombre) . ' ' . $this->anio;

        return [
            $alumno->matriculaA ?? '—',
            $nombreCompleto ?: '—',
            $concepto_adeudo,
            $alumno->diplomado?->nombre_diplomado ?? '—',
            ucfirst($mesNombre) . ' ' . $this->anio,
        ];
    }
}