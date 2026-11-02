<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    //
    protected $table = 'solicitudes';

    protected $fillable = [
        'nombre', 'email', 'telefono', 'area', 'descripcion', 'estado'
    ];
}
