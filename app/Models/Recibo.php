<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Recibo extends Model
{
    protected $table = 'reciboss';              // o 'reciboss' si así se llama
    protected $primaryKey = 'id_recibo';
    public $timestamps = false;                // tu tabla no tiene created_at/updated_at

    protected $fillable = [
        'id_alumno',
        'fecha_pago',
        'concepto',
        'monto',
        'comprobante_path',   // ruta pública (Storage::url)
        'estatus',            // pendiente|validado|rechazado
        'fecha_validacion',
        'validado_por',
        'comentarios',
    ];

    protected $casts = [
        'fecha_pago'       => 'date',
        'fecha_validacion' => 'datetime',
        'monto'            => 'decimal:2',
    ];

    // Relación: el recibo pertenece a un alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id_alumno');
    }

    // Relación: usuario que validó
    public function validador()
    {
        return $this->belongsTo(User::class, 'validado_por', 'id_usuario');
    }

    // Accesor opcional para URL absoluta del comprobante
    protected function comprobanteUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->comprobante_path; // si ya guardas Storage::url(...)
        });
    }
}
