<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false; 

    protected $fillable = [
        'nombre', 'apellidoP', 'apellidoM', 'fecha_nac', 'usuario', 'pass', 'genero',
        'correo', 'telefono', 'direccion', 'id_rol', 'fecha_registro'
    ];

    protected $hidden = ['pass'];

    protected $casts = [
        'fecha_nac' => 'date',
    ];

    public function getAuthPassword()
    {
        return $this->pass;
    }

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'id_usuario', 'id_usuario');
    }

    public function docente()
    {
        return $this->hasOne(\App\Models\Docente::class, 'id_usuario', 'id_usuario');
    }

    public function alumno()
    {
        return $this->hasOne(\App\Models\Alumno::class, 'id_usuario', 'id_usuario');
    }

    public function aspirante()
    {
        return $this->hasOne(\App\Models\Aspirante::class, 'id_usuario', 'id_usuario');
    }
}