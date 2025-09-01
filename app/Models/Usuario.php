<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false; // no created_at / updated_at

    protected $fillable = [
        'nombre','apellidoP','apellidoM','fecha_nac','usuario','pass',
        'genero','correo','telefono','direccion','id_rol'
    ];

    protected $casts = [
        'fecha_nac' => 'date',
    ];

    public function alumno(){
        // 1:1 (usuarios.id_usuario -> alumnos.id_usuario)
        return $this->hasOne(Alumno::class, 'id_usuario', 'id_usuario');
    }

    public function docente(){
        // 1:1 (usuarios.id_usuario -> docentes.id_usuario)
        return $this->hasOne(Docente::class, 'id_usuario', 'id_usuario');
    }
}
