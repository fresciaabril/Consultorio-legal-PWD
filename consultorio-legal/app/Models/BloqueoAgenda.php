<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloqueoAgenda extends Model
{
    protected $table = 'bloqueos_agenda';

    protected $fillable = [
        'user_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'motivo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function abogado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
