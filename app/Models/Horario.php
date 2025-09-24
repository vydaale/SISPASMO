<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'hora_inicio',
        'hora_fin',
        'modalidad',
        'aula',
        'id_diplomado',
        'id_modulo',
        'id_docente',
    ];

    public function diplomado()
    {
        return $this->belongsTo(Diplomado::class, 'id_diplomado');
    }

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'id_modulo');
    }

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente');
    }
}