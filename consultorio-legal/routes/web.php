
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SolicitudController;
use App\Models\Solicitud;
use App\Http\Controllers\RegistroController;

// PÁGINA DE INICIO

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

    // Solo pueden ingresar los clientes
    abort_unless(auth()->user()->rol === 'cliente', 403);

    // Buscamos las solicitudes del cliente según su email
    $solicitudes = Solicitud::where(
        'email',
        auth()->user()->email
    )->latest()->get();

    return view('perfiles.cliente', compact('solicitudes'));

})->middleware('auth')->name('perfil');


// PANEL DEL ABOGADO

Route::get('/abogado', function () {

    // Solo puede ingresar el abogado
    abort_unless(auth()->user()->rol === 'abogado', 403);

    // Obtenemos todas las solicitudes reales
    $solicitudes = Solicitud::latest()->get();

    return view('perfiles.abogado', compact('solicitudes'));

})->middleware('auth')->name('abogado');


// SOLICITUDES - MALE

// Formulario para nuevos clientes
Route::get('/solicitudes/crear', [SolicitudController::class, 'create']);

Route::post('/solicitudes', [SolicitudController::class, 'store']);

// Listado de solicitudes: solo abogado
Route::get('/solicitudes', function () {

    abort_unless(auth()->user()->rol === 'abogado', 403);

    return app(SolicitudController::class)->index();

})->middleware('auth');

// Aceptar solicitud: solo abogado
Route::patch('/solicitudes/{solicitud}/aceptar', function (Solicitud $solicitud) {

    abort_unless(auth()->user()->rol === 'abogado', 403);

    return app(SolicitudController::class)->aceptar($solicitud);

})->middleware('auth');

// Rechazar solicitud: solo abogado
Route::patch('/solicitudes/{solicitud}/rechazar', function (Solicitud $solicitud) {

    abort_unless(auth()->user()->rol === 'abogado', 403);

    return app(SolicitudController::class)->rechazar($solicitud);

})->middleware('auth');


// DETALLE DE CONSULTA DEL CLIENTE

Route::get('/consultas/cliente/{id}', function ($id) {

    abort_unless(auth()->user()->rol === 'cliente', 403);

    // Buscamos la solicitud real y verificamos su email
    $solicitud = Solicitud::where('email', auth()->user()->email)
        ->findOrFail($id);

    return view('consultas.ver_cliente', compact('solicitud'));

})->middleware('auth');


// DETALLE DE CONSULTA DEL ABOGADO

Route::get('/consultas/abogado/{id}', function ($id) {

    abort_unless(auth()->user()->rol === 'abogado', 403);

    // Buscamos la solicitud real en Aiven
    $solicitud = Solicitud::findOrFail($id);

    return view('consultas.ver_abogado', compact('solicitud'));

})->middleware('auth');


// DEMO DE JITSI

Route::get('/consulta-demo', function () {

    return view('consultas.demo');

})->middleware('auth');


// SEGUIMIENTO DE SOLICITUDES

// Consultar el estado de una solicitud mediante su token
Route::get('/solicitudes/seguimiento/{token}',
    [SolicitudController::class, 'seguimiento'])
    ->name('solicitudes.seguimiento');

// Solicitar el código de verificación por correo
Route::post('/solicitudes/seguimiento/{token}/enviar-codigo',
    [SolicitudController::class, 'enviarCodigo'])
    ->middleware('throttle:3,10')
    ->name('solicitudes.enviarCodigo');

// NUEVA RUTA: verificar el código de 6 dígitos
Route::post('/solicitudes/seguimiento/{token}/verificar-codigo',
    [SolicitudController::class, 'verificarCodigo'])
    ->middleware('throttle:5,10')
    ->name('solicitudes.verificarCodigo');

// Mostrar el formulario de registro
Route::get('/registro', [RegistroController::class, 'mostrarRegistro'])
    ->name('registro.mostrar');

// Guardar el nuevo cliente en Aiven
Route::post('/registro', [RegistroController::class, 'guardarRegistro'])
    ->name('registro.guardar');