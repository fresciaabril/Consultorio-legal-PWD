<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('especialidades')->nullable()->after('rol');
            $table->text('descripcion_inicio')->nullable()->after('especialidades');
            $table->string('foto')->nullable()->after('descripcion_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['especialidades', 'descripcion_inicio', 'foto']);
        });
    }
};