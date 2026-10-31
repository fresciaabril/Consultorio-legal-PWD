<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultorioController;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/consulta-demo', function () {
    return view('consultas.demo');
});

// Rutas de tu módulo
Route::get('/perfil/cliente', [ConsultorioController::class, 'cliente']);
Route::get('/perfil/abogado', [ConsultorioController::class, 'abogado']);