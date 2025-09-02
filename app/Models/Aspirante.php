<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspirante extends Model {
    protected $table = 'aspirantes';
    protected $primaryKey = 'id_aspirante';
    public $timestamps = false;
    protected $fillable = ['id_usuario','interes','dia','estatus'];
    public function usuario(){ return $this->belongsTo(User::class,'id_usuario','id_usuario'); }
}