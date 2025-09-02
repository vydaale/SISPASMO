<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queja extends Model
{
    protected $table = 'quejas';
    protected $primaryKey = 'id_queja';
    public $timestamps = false;

    protected $fillable = ['id_usuario', 'mensaje', 'tipo', 'contacto'];

    public function usuario()
    {
        // usuarios.id_usuario -> quejas.id_usuario
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id_usuario');
    }
}
