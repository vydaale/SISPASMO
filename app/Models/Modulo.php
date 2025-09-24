<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Modulo extends Model
{
    protected $table = 'modulos';
    protected $primaryKey = 'id_modulo';
    public $timestamps = false;

    protected $fillable = ['numero_modulo','nombre_modulo','descripcion','duracion','estatus','url'];
    
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_diplomado');
    }
}