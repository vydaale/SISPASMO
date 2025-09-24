<?php
// app/Models/Alergia.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alergia extends Model
{
    protected $table = 'alergias';
    protected $primaryKey = 'id_alergias';
    public $timestamps = false;

    protected $fillable = [
        'polvo',
        'polen',
        'alimentos',
        'alimentos_detalle',
        'animales',
        'animales_detalle',
        'insectos',
        'insectos_detalle',
        'medicamentos',
        'medicamentos_detalle',
        'otro',
        'otro_detalle',
    ];

    protected $casts = [
        'polvo' => 'boolean',
        'polen' => 'boolean',
        'alimentos' => 'boolean',
        'animales' => 'boolean',
        'insectos' => 'boolean',
        'medicamentos' => 'boolean',
        'otro' => 'boolean',
    ];
}
