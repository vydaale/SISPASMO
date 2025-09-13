<?php
// app/Models/Enfermedad.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enfermedad extends Model
{
    protected $table = 'enfermedades';
    protected $primaryKey = 'id_enfermedades';
    public $timestamps = false;

    protected $fillable = [
        'enfermedad_cronica',
        'enfermedad_cronica_detalle',
        'toma_medicamentos',
        'toma_medicamentos_detalle',
        'visita_medico',
        'visita_medico_detalle',
        'nombre_medico',
        'telefono_medico',
    ];

    protected $casts = [
        'enfermedad_cronica' => 'boolean',
        'toma_medicamentos'  => 'boolean',
        'visita_medico'      => 'boolean',
    ];
}
