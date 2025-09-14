<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumnos';
    protected $primaryKey = 'id_alumno';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario','matriculaA','num_diplomado','grupo','estatus'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function fichaMedica()
    {
        return $this->hasOne(FichaMedica::class, 'id_alumno', 'id_alumno');
    }
}
