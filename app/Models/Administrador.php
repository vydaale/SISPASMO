<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administradores';
    protected $primaryKey = 'id_admin';
    public $timestamps = false;

    protected $fillable = ['id_usuario','fecha_ingreso','rol','estatus'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}
