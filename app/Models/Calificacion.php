<?php
// app/Models/Calificacion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';
    protected $primaryKey = 'id_calif';
    public $timestamps = false;

    protected $fillable = [
        'id_alumno',
        'id_modulo',
        'id_docente',
        'tipo',
        'observacion',
        'calificacion'
    ];

    protected $casts = [
        'calificacion' => 'decimal:2',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class,  'id_alumno',  'id_alumno');
    }
    public function modulo()
    {
        return $this->belongsTo(Modulo::class,  'id_modulo',  'id_modulo');
    }
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }
}