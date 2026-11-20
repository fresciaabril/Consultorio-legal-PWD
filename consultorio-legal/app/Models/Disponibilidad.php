<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disponibilidad extends Model
{
    protected $table = 'disponibilidades';

    protected $fillable = [
        'user_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function abogado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
