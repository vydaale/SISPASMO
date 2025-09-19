<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diplomado extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_diplomado';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'grupo',
        'tipo',
        'capacidad',
        'fecha_inicio',
        'fecha_fin'
    ];
    
    public function alumnos()
    {
    return $this->hasMany(Alumno::class, 'id_diplomado', 'id_diplomado');
}
}