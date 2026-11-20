<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; // Necesario para procesar los datos enviados desde los formularios
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SolicitudController;
use App\Models\Solicitud;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\BloqueoAgendaController;


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

    // Cargamos los turnos simulados guardados en sesión mientras Agus termina TurnoController
    $turnos = session('turnos_prueba', []);

    return view('perfiles.cliente', compact('solicitudes', 'turnos'));

})->middleware('auth')->name('perfil');


// PANEL DEL ABOGADO
Route::get('/abogado', function () {

    // Solo puede ingresar el abogado
    abort_unless(auth()->user()->rol === 'abogado', 403);

    // Obtenemos todas las solicitudes reales
    $solicitudes = Solicitud::latest()->get();

    return view('perfiles.abogado', compact('solicitudes'));

})->middleware('auth')->name('abogado');


// PERFIL EDITABLE DEL ABOGADO (Protegido con auth y rol abogado)
Route::get('/abogado/perfil', function () {

    // Solo puede ingresar el abogado
    abort_unless(auth()->user()->rol === 'abogado', 403);

    $usuario = auth()->user();

    // Carga los datos guardados en sesión o usa por defecto los datos profesionales
    $perfil = session('perfil_abogado', [
        'nombre' => $usuario->name ?? 'Dr. Martín Alejandro Fernández',
        'especialidades' => 'Derecho Civil, Laboral y de Familia',
        'email' => $usuario->email ?? 'abogado@estudiojuridico.com',
        'descripcion_inicio' => 'Abogado egresado con experiencia en asesoramiento integral, resolución de conflictos civiles, reclamos laborales y derecho de familia. Atención personalizada presencial y mediante consultas virtuales.'
    ]);

    return view('perfiles.editar', compact('perfil'));

})->middleware('auth')->name('abogado.perfil');

Route::put('/abogado/perfil', function (Request $request) {

    // Solo puede guardar cambios el abogado
    abort_unless(auth()->user()->rol === 'abogado', 403);

    $datos = $request->validate([
        'nombre' => 'required|string|max:120',
        'especialidades' => 'required|string|max:200',
        'email' => 'required|email',
        'descripcion_inicio' => 'required|string|max:1000',
    ]);

    // Guarda los datos en la sesión para reflejarlos en el perfil y en la página de inicio
    session(['perfil_abogado' => $datos]);

    return redirect()->route('abogado.perfil')->with('exito', 'Tus datos y la descripción del inicio se actualizaron correctamente.');

})->middleware('auth')->name('abogado.perfil.actualizar');


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

// Verificar el código de 6 dígitos
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

 // TURNOS - AGUS

// Consultar los turnos del usuario autenticado
Route::get('/turnos', [TurnoController::class, 'index'])
    ->middleware('auth')
    ->name('turnos.index');

// Cancelar turnos (solo el abogado autorizado)
Route::delete('/turnos/{turno}', [TurnoController::class, 'destroy'])
    ->middleware('auth')
    ->name('turnos.destroy');

// Reservar turnos
Route::post('/turnos', [TurnoController::class, 'store'])
    ->middleware('auth')
    ->name('turnos.store');

// DISPONIBILIDAD DEL ABOGADO - MALE
// Consultar los horarios de atención configurados
Route::get('/abogado/disponibilidades', [DisponibilidadController::class, 'index'])
    ->middleware('auth')
    ->name('abogado.disponibilidades');

// Guardar un horario de atención
Route::post('/abogado/disponibilidades', [DisponibilidadController::class, 'store'])
    ->middleware('auth')
    ->name('abogado.disponibilidades.store');

// Consultar los horarios libres
Route::get('/abogado/horarios-libres', [DisponibilidadController::class, 'horariosLibres'])
    ->middleware('auth')
    ->name('abogado.horarios-libres');

// Mostrar la configuración de la agenda
Route::get('/abogado/agenda', [DisponibilidadController::class, 'configurar'])
    ->middleware('auth')
    ->name('abogado.agenda');

// Eliminar un horario de disponibilidad
Route::delete('/abogado/disponibilidades/{id}', [DisponibilidadController::class, 'destroy'])
    ->middleware('auth')
    ->name('abogado.disponibilidades.destroy');

// BLOQUEOS DE AGENDA - MALE
// Guardar un bloqueo de agenda
Route::post('/abogado/bloqueos', [BloqueoAgendaController::class, 'store'])
    ->middleware('auth')
    ->name('abogado.bloqueos.store');

// Consultar los bloqueos de agenda
Route::get('/abogado/bloqueos', [BloqueoAgendaController::class, 'index'])
    ->middleware('auth')
    ->name('abogado.bloqueos');

// Eliminar un bloqueo
Route::delete('/abogado/bloqueos/{id}', [BloqueoAgendaController::class, 'destroy'])
    ->middleware('auth')
    ->name('abogado.bloqueos.destroy');
?>