<?php

namespace App\Exports;

use App\Models\Recibo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PagosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Recibo::with(['alumno', 'alumno.diplomado'])
            ->whereBetween('fecha_pago', [$this->fechaInicio, $this->fechaFin])
            ->where('estatus', 'validado')
            ->orderBy('fecha_pago')
            ->get();
    }

    /**
     * Define las cabeceras de las columnas.
     * @return array
     */
    public function headings(): array
    {
        return [
            'Alumno',
            'Matrícula',
            'Diplomado',
            'Concepto',
            'Monto',
            'Fecha de Pago',
        ];
    }

    /**
     * Mapea los datos de cada registro a las columnas.
     * @param mixed $pago
     * @return array
     */
    public function map($pago): array
    {
        return [
            $pago->alumno->nombre ?? 'N/A',
            $pago->alumno->matriculaA ?? 'N/A',
            $pago->alumno->diplomado->nombre_diplomado ?? 'N/A',
            $pago->concepto,
            number_format($pago->monto, 2),
            $pago->fecha_pago->format('Y-m-d'),
        ];
    }
}