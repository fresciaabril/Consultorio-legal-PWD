<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bloqueos_agenda', function (Blueprint $table) {
            $table->id();

            // Abogado que bloquea su agenda
             $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Fecha que se quiere bloquear
            $table->date('fecha');

            // Si son NULL, se bloquea el día completo
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            // Motivo opcional
            $table->string('motivo')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloqueos_agenda');
    }
};
