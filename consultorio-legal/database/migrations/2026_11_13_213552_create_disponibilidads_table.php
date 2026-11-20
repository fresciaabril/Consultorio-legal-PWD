

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
        Schema::create('disponibilidades', function (Blueprint $table) {
            $table->id();
            // Abogado al que pertenece el horario
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

              // Día de la semana: 1 = lunes, 7 = domingo
            $table->unsignedTinyInteger('dia_semana');

            // Horario de atención
            $table->time('hora_inicio');
            $table->time('hora_fin');


            $table->timestamps();
            $table->index(['user_id', 'dia_semana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disponibilidades');
    }
};
