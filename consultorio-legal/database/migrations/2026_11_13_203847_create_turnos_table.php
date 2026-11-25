<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id(); //Crea una clave primaria autoincremental, cada turno tendrá su propio identificador

            // Relaciones con los usuarios
            $table->foreignId('cliente_id') //Crea una clave foránea que apunta al id de la tabla users el mismo id del usuario para ek clienteid
                ->constrained('users')
                ->restrictOnDelete(); //Impide eliminar un usuario que tenga turnos asociados

            $table->foreignId('abogado_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Fecha y horario de la consulta
            $table->dateTime('inicio');
            $table->dateTime('fin');

            // Información del turno
            $table->enum('modalidad', ['virtual', 'presencial']);
            $table->string('estado')->default('reservado');
            $table->text('motivo')->nullable();

            // Fechas automáticas de Laravel
            $table->timestamps();

            // Índices para consultar la agenda
            $table->index(['abogado_id', 'inicio']);
            $table->index(['cliente_id', 'inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};
