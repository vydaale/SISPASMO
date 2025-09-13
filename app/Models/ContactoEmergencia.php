<?php
// app/Models/ContactoEmergencia.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoEmergencia extends Model
{
    protected $table = 'contactos_emergencia';
    protected $primaryKey = 'id_contacto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellidos',
        'domicilio',
        'telefono',
        'parentesco',
        'institucion',
    ];
}
