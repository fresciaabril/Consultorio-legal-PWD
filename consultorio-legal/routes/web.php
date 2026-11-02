
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SolicitudController;
use App\Models\Solicitud;

// Página de inicio
Route::get('/', function () {
    return view('inicio');
});

// Login
Route::get('/login', [AuthController::class, 'mostrarLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'iniciarSesion'])
    ->name('login.procesar');

// Cerrar sesión
Route::post('/logout', [AuthController::class, 'cerrarSesion'])
    ->name('logout');

// Perfil del cliente
Route::get('/perfil', function () {
    $solicitudes = Solicitud::where(
        'email',
        auth()->user()->email
    )->latest()->get();

    return view('perfiles.cliente', compact('solicitudes'));
})->middleware('auth')->name('perfil');

// Panel del abogado
Route::get('/abogado', function () {
    return view('perfiles.abogado');
})->middleware('auth')->name('abogado');

// Solicitudes
Route::get('/solicitudes/crear', [SolicitudController::class, 'create']);

Route::post('/solicitudes', [SolicitudController::class, 'store']);

Route::get('/solicitudes', [SolicitudController::class, 'index']);

Route::patch('/solicitudes/{solicitud}/aceptar', [SolicitudController::class, 'aceptar']);

Route::patch('/solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'rechazar']);

// Demo de Jitsi
Route::get('/consulta-demo', function () {
    return view('consultas.demo');
});
