<?php

namespace App\Exports;

use App\Models\Aspirante;
use App\Models\Diplomado; 
use Maatwebsite\Excel\Concerns\FromQuery; 
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class AspirantesExport implements FromQuery, WithHeadings, WithMapping 
{
    private string $modo;
    private string $tipo;
    
    /*
     *$modo: 'total' | 'comparacion'
     *$tipo: 'basico' | 'intermedio y avanzado' | 'todos'  (solo para 'total')
    */
    public function __construct(
        string $modo = 'total',
        string $tipo = 'todos'
    ) {
        /* Asigna los valores a las propiedades declaradas. */
        $this->modo = $modo;
        $this->tipo = $tipo;
    }

    public string $fileName = 'Aspirantes.xlsx';

    public function query()
    {
    $query = Aspirante::query()
        ->join('usuarios', 'aspirantes.id_usuario', '=', 'usuarios.id_usuario');
    $tiposConocidos = ['basico', 'intermedio y avanzado'];
    
    /* Lógica para el modo 'comparacion'. */
    if ($this->modo === 'comparacion') {
        /* Trae aspirantes interesados en tipos básicos E intermedio/avanzado. */
        $nombresDiplomados = Diplomado::whereIn('tipo', $tiposConocidos)->pluck('nombre');
        $query->whereIn('aspirantes.interes', $nombresDiplomados); // Usar alias de tabla aquí.
    }
    /* Lógica para el modo 'total' */
    else {
        if ($this->tipo !== 'todos' && $this->tipo !== '') {
            /* Se selecciona un tipo específico ('basico' o 'intermedio y avanzado'). */
            $nombresDiplomados = Diplomado::where('tipo', $this->tipo)->pluck('nombre');
            $query->whereIn('aspirantes.interes', $nombresDiplomados); // Usar alias de tabla aquí.
        } else {
            /* Se selecciona 'Todos los tipos de diplomado' (pero solo los interesados). */
            /* Se filtran por todos los tipos conocidos (similar a la comparación, pero en modo 'total'). */
            $nombresDiplomados = Diplomado::whereIn('tipo', $tiposConocidos)->pluck('nombre');
            $query->whereIn('aspirantes.interes', $nombresDiplomados); // Usar alias de tabla aquí.
        }
    }
    
    $query->select([
        'usuarios.nombre as user_nombre', 'usuarios.correo as user_correo', 'aspirantes.interes',]);
    
    $query->orderBy('aspirantes.interes')->orderBy('usuarios.nombre');

    return $query;
    }

    public function map($row): array
    {
        return [
            $row->user_nombre,
            $row->user_correo,
            $row->interes, 
        ];
    }
    
    public function headings(): array
    {
        return ['Nombre', 'Correo', 'Diplomado de interés'];
    }
}