<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinador extends Model{
    protected $table = 'coordinadores';
    protected $primaryKey = 'id_coordinador';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario','fecha_ingreso','estatus'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
}