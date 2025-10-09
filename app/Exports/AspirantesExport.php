<?php

namespace App\Exports;

use App\Models\Aspirante;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AspirantesExport implements FromCollection, WithHeadings, WithMapping, Responsable
{
    /**
     * $modo: 'total' | 'comparacion'
     * $tipo: 'basico' | 'intermedio y avanzado' | 'todos'  (solo para 'total')
     */
    public function __construct(
        private string $modo = 'total',
        private string $tipo = 'todos'
    ) {}

    public string $fileName = 'Aspirantes.xlsx';

    public function collection()
    {
        if ($this->modo === 'comparacion') {
            // Ambos tipos
            return Aspirante::query()
                ->whereIn('interes', ['basico', 'intermedio y avanzado'])
                ->orderBy('interes')
                ->orderBy('nombre')
                ->get(['nombre', 'correo', 'interes']);
        }

        // modo total con filtro
        if ($this->tipo === 'todos' || $this->tipo === '') {
            return Aspirante::query()
                ->orderBy('interes')
                ->orderBy('nombre')
                ->get(['nombre', 'correo', 'interes']);
        }

        // Filtro específico
        return Aspirante::query()
            ->where('interes', $this->tipo)
            ->orderBy('nombre')
            ->get(['nombre', 'correo', 'interes']);
    }

    public function headings(): array
    {
        return ['Nombre', 'Correo', 'Diplomado de interés'];
    }

    public function map($row): array
    {
        // Mapear valor bonito si lo deseas:
        $labels = [
            'basico' => 'Diplomado nivel básico',
            'intermedio y avanzado' => 'Diplomado intermedio y avanzado',
        ];
        $interes = $labels[$row->interes] ?? $row->interes;

        return [
            $row->nombre,
            $row->correo,
            $interes,
        ];
    }
}
