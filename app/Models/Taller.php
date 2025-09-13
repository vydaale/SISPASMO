<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Taller extends Model{
    protected $table = 'extracurricular';
    protected $primaryKey = 'id_extracurricular';
    public $timestamps = false;

    protected $fillable = ['nombre_act','responsable','fecha','tipo','hora_inicio','hora_fin','lugar','modalidad','estatus','capacidad',
    'descripcion','material','url'];
}