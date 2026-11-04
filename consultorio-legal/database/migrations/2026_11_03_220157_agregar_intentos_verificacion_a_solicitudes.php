<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {

            // Cantidad de intentos incorrectos del código
            $table->unsignedInteger('intentos_verificacion')
            ->default(0);

        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {

            $table->dropColumn('intentos_verificacion');

        });
    }
};
