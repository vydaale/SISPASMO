<?php
// app/Models/FichaMedica.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FichaMedica extends Model
{
    protected $table = 'ficha_medica';
    protected $primaryKey = 'id_ficha';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'id_alergias', 'id_enfermedades', 'id_contacto'];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }
    public function alergias()
    {
        return $this->belongsTo(Alergia::class, 'id_alergias', 'id_alergias');
    }
    public function enfermedades()
    {
        return $this->belongsTo(Enfermedad::class, 'id_enfermedades', 'id_enfermedades');
    }
    public function contacto()
    {
        return $this->belongsTo(ContactoEmergencia::class, 'id_contacto', 'id_contacto');
    }
}
