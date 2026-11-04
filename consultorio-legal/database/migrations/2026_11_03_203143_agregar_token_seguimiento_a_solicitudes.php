
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
        Schema::table('solicitudes', function (Blueprint $table) {

            // Agregamos un token para identificar cada solicitud
            $table->string('token_seguimiento', 64)
                  ->nullable()
                  ->unique();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {

            // Eliminamos la columna si revertimos la migración
            $table->dropColumn('token_seguimiento');

        });
    }
};
