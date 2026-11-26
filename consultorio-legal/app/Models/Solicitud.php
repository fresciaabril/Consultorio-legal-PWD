<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    protected $table = 'solicitudes';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'area',
        'descripcion',
        'estado',
        'token_seguimiento'
    ];

    protected $casts = [
        'codigo_expira' => 'datetime',
        'email_verificado' => 'datetime',
    ];

    // Una solicitud puede tener varios turnos
    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class, 'solicitud_id');
    }
}
