<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {

            // Código de verificación protegido
            $table->string('codigo_verificacion')->nullable();

            // Fecha y hora de vencimiento del código
            $table->timestamp('codigo_expira')->nullable();

            // Fecha en que el cliente verificó su correo
            $table->timestamp('email_verificado')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {

            $table->dropColumn([
                'codigo_verificacion',
                'codigo_expira',
                'email_verificado'
            ]);

        });
    }
};
