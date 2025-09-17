<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table = 'citas';
    protected $primaryKey = 'id_cita';
    public $timestamps = false;

    protected $fillable = [
        'fecha_cita','hora_cita','estatus','lugar',
        'id_aspirante','id_coordinador'
    ];
    
    public function aspirante(){ 
        return $this->belongsTo(\App\Models\Aspirante::class,'id_aspirante','id_aspirante'); 
    }

    public function coordinador(){ 
        return $this->belongsTo(\App\Models\Coordinador::class,'id_coordinador','id_coordinador'); 
    }
}
