<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultorioController;

Route::get('/', function () {
    return view('inicio');
});

// Rutas principales del módulo
Route::get('/perfil/cliente', [ConsultorioController::class, 'cliente']);
Route::get('/perfil/abogado', [ConsultorioController::class, 'abogado']);

// 1. Ruta de detalle que ve el CLIENTE
Route::get('/consultas/cliente/{id}', function ($id) {
    $consultasSimuladas = [
        1 => (object)['id' => 1, 'cliente' => 'María García', 'area' => 'Derecho Civil', 'descripcion' => 'Consulta sobre alquiler de vivienda y renovación.', 'estado' => 'aceptada'],
        2 => (object)['id' => 2, 'cliente' => 'Juan Pérez', 'area' => 'Derecho Laboral', 'descripcion' => 'Duda sobre liquidación final.', 'estado' => 'pendiente']
    ];

    $solicitud = $consultasSimuladas[$id] ?? (object)['id' => $id, 'cliente' => 'Cliente', 'area' => 'General', 'descripcion' => 'Sin detalle', 'estado' => 'pendiente'];

    return view('consultas.ver_cliente', compact('solicitud'));
});

// 2. Ruta de detalle que ve el ABOGADO
Route::get('/consultas/abogado/{id}', function ($id) {
    $consultasSimuladas = [
        1 => (object)['id' => 1, 'cliente' => 'María García', 'area' => 'Derecho Civil', 'descripcion' => 'Consulta sobre alquiler de vivienda y renovación.', 'estado' => 'aceptada'],
        2 => (object)['id' => 2, 'cliente' => 'Juan Pérez', 'area' => 'Derecho Laboral', 'descripcion' => 'Duda sobre liquidación final.', 'estado' => 'pendiente']
    ];

    $solicitud = $consultasSimuladas[$id] ?? (object)['id' => $id, 'cliente' => 'Cliente', 'area' => 'General', 'descripcion' => 'Sin detalle', 'estado' => 'pendiente'];

    return view('consultas.ver_abogado', compact('solicitud'));
});