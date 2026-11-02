<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultorioController;

Route::get('/', function () {
    return view('inicio');
});

Route::get('/perfil/cliente', [ConsultorioController::class, 'cliente']);
Route::get('/perfil/abogado', [ConsultorioController::class, 'abogado']);

// Ruta dinámica de detalles de la consulta
Route::get('/consultas/{id}', function ($id) {
    $consultasSimuladas = [
        1 => (object)[
            'id' => 1,
            'cliente' => 'María García',
            'area' => 'Derecho Civil',
            'descripcion' => 'Consulta sobre alquiler de vivienda y renovación del contrato.',
            'estado' => 'aceptada'
        ],
        2 => (object)[
            'id' => 2,
            'cliente' => 'Juan Pérez',
            'area' => 'Derecho Laboral',
            'descripcion' => 'Duda sobre liquidación final por despido.',
            'estado' => 'pendiente'
        ]
    ];

    $solicitud = $consultasSimuladas[$id] ?? (object)[
        'id' => $id,
        'cliente' => 'Cliente de Prueba',
        'area' => 'Consulta General',
        'descripcion' => 'Sin detalles adicionales.',
        'estado' => 'pendiente'
    ];

    return view('consultas.demo', compact('solicitud', 'id'));
});