<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;

class Alumno extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_alumno';
    public $timestamps = false;

    protected $fillable = ['id_usuario', 'matriculaA', 'id_diplomado', 'estatus'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function diplomado()
    {
        return $this->belongsTo(Diplomado::class, 'id_diplomado', 'id_diplomado');
    }

    public function fichaMedica()
    {
        return $this->hasOne(FichaMedica::class, 'id_alumno', 'id_alumno');
    }

    public function getEmailForPasswordReset()
    {
        return $this->usuario?->correo;
    }
}
