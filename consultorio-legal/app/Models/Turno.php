<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Turno extends Model
{
    // Campos que permitimos asignar masivamente
    protected $fillable = [
        'cliente_id',
        'abogado_id',
        'inicio',
        'fin',
        'modalidad',
        'estado',
        'motivo',
    ];

    // Le indica a Eloquent que trate esos campos como fechas
    protected function casts(): array
    {
        return [
            'inicio' => 'datetime:Y-m-d H:i:s',
            'fin' => 'datetime:Y-m-d H:i:s',
        ];
    }

    // Un turno pertenece a un cliente
    public function cliente(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    // Un turno pertenece a un abogado
    public function abogado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abogado_id');
    }
}
