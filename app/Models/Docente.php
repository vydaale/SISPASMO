<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docentes';
    protected $primaryKey = 'id_docente';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario','matriculaD','cedula','especialidad','salario'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
    
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_diplomado');
    }
    
    public function getEmailForPasswordReset()
    {
        return $this->usuario->email;
    }
}
