<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ConsultorioController;
use App\Models\Solicitud;

// Página de inicio
Route::get('/', function () {
    return view('inicio');
});

// LOGIN

Route::get('/login', [AuthController::class, 'mostrarLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'iniciarSesion'])
    ->name('login.procesar');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'cerrarSesion'])
    ->middleware('auth')
    ->name('logout');


// PERFIL DEL CLIENTE

Route::get('/perfil', function () {
    $solicitudes = Solicitud::where(
        'email',
        auth()->user()->email
    )->latest()->get();

    return view('perfiles.cliente', compact('solicitudes'));
})->middleware('auth')->name('perfil');


// PANEL DEL ABOGADO

Route::get('/abogado', function () {
    return view('perfiles.abogado');
})->middleware('auth')->name('abogado');


// SOLICITUDES - MALE

Route::get('/solicitudes/crear', [SolicitudController::class, 'create']);

Route::post('/solicitudes', [SolicitudController::class, 'store']);

Route::get('/solicitudes', [SolicitudController::class, 'index']);

Route::patch('/solicitudes/{solicitud}/aceptar', [SolicitudController::class, 'aceptar']);

Route::patch('/solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'rechazar']);


// PERFILES DE PRUEBA - FRE

Route::get('/perfil/cliente', [ConsultorioController::class, 'cliente']);

Route::get('/perfil/abogado', [ConsultorioController::class, 'abogado']);


// CONSULTAS DEL CLIENTE - FRE

Route::get('/consultas/cliente/{id}', function ($id) {

    $consultasSimuladas = [
        1 => (object)[
            'id' => 1,
            'cliente' => 'María García',
            'area' => 'Derecho Civil',
            'descripcion' => 'Consulta sobre alquiler de vivienda y renovación.',
            'estado' => 'aceptada'
        ],
        2 => (object)[
            'id' => 2,
            'cliente' => 'Juan Pérez',
            'area' => 'Derecho Laboral',
            'descripcion' => 'Duda sobre liquidación final.',
            'estado' => 'pendiente'
        ]
    ];

    $solicitud = $consultasSimuladas[$id] ?? (object)[
        'id' => $id,
        'cliente' => 'Cliente',
        'area' => 'General',
        'descripcion' => 'Sin detalle',
        'estado' => 'pendiente'
    ];

    return view('consultas.ver_cliente', compact('solicitud'));

});


// CONSULTAS DEL ABOGADO - FRE

Route::get('/consultas/abogado/{id}', function ($id) {

    $consultasSimuladas = [
        1 => (object)[
            'id' => 1,
            'cliente' => 'María García',
            'area' => 'Derecho Civil',
            'descripcion' => 'Consulta sobre alquiler de vivienda y renovación.',
            'estado' => 'aceptada'
        ],
        2 => (object)[
            'id' => 2,
            'cliente' => 'Juan Pérez',
            'area' => 'Derecho Laboral',
            'descripcion' => 'Duda sobre liquidación final.',
            'estado' => 'pendiente'
        ]
    ];

    $solicitud = $consultasSimuladas[$id] ?? (object)[
        'id' => $id,
        'cliente' => 'Cliente',
        'area' => 'General',
        'descripcion' => 'Sin detalle',
        'estado' => 'pendiente'
    ];

    return view('consultas.ver_abogado', compact('solicitud'));

});


// DEMO DE JITSI

Route::get('/consulta-demo', function () {
    return view('consultas.demo');
});
