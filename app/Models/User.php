<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'nombre','apellidoP','apellidoM','fecha_nac','usuario','pass','genero',
        'correo','telefono','direccion','id_rol'
    ];

    protected $hidden = ['pass'];

    // Laravel por defecto busca un atributo 'password'
    public function getAuthPassword()
    {
        return $this->pass;
    }

    // Relaciones útiles
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'id_usuario', 'id_usuario');
    }
}
