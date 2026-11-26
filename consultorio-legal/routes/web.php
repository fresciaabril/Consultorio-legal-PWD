<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SolicitudController;
use App\Models\Solicitud;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\BloqueoAgendaController;


// PÁGINA DE INICIO (carga los datos del abogado desde la base de datos)
Route::get('/', function () {
    $abogado = \App\Models\User::where('rol', 'abogado')->first();

    return view('inicio', compact('abogado'));
});


// LOGIN Y REGISTRO
Route::get('/login', [AuthController::class, 'mostrarLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'iniciarSesion'])
    ->name('login.procesar');

Route::post('/logout', [AuthController::class, 'cerrarSesion'])
    ->middleware('auth')
    ->name('logout');

Route::get('/register', function () {
    return redirect()->route('registro.mostrar');
});

Route::get('/registro', [RegistroController::class, 'mostrarRegistro'])
    ->name('registro.mostrar');

Route::post('/registro', [RegistroController::class, 'guardarRegistro'])
    ->name('registro.guardar');


// PERFIL DEL CLIENTE
Route::get('/perfil', function () {
    abort_unless(auth()->user()->rol === 'cliente', 403);

    $solicitudes = Solicitud::where(
        'email',
        auth()->user()->email
    )->latest()->get();

    $abogado = \App\Models\User::where('rol', 'abogado')->first();

    $turnos = \App\Models\Turno::with('abogado')
        ->where('cliente_id', auth()->id())
        ->orderBy('inicio', 'desc')
        ->get();

    return view('perfiles.cliente', compact('solicitudes', 'turnos', 'abogado'));
})->middleware('auth')->name('perfil');


// PANEL DEL ABOGADO
Route::get('/abogado', function () {
    abort_unless(auth()->user()->rol === 'abogado', 403);

    // Trae todas las consultas EXCEPTO las rechazadas
    $solicitudes = \App\Models\Solicitud::where('estado', '!=', 'rechazada')
        ->latest()
        ->get();

    $turnos = \App\Models\Turno::with('cliente')
        ->where('abogado_id', auth()->id())
        ->orderBy('inicio', 'asc')
        ->get();

    return view('perfiles.abogado', compact('solicitudes', 'turnos'));
})->middleware('auth')->name('abogado');


// PERFIL EDITABLE DEL ABOGADO (lee desde la base de datos)
Route::get('/abogado/perfil', function () {

    abort_unless(auth()->user()->rol === 'abogado', 403);

    $usuario = auth()->user();

    $perfil = [
        'nombre'             => $usuario->name ?? 'Dr. Martín Alejandro Fernández',
        'especialidades'     => $usuario->especialidades ?? 'Derecho Civil, Laboral y de Familia',
        'email'              => $usuario->email ?? 'abogado@estudiojuridico.com',
        'descripcion_inicio' => $usuario->descripcion_inicio ?? 'Brindamos acompañamiento jurídico integral con atención personalizada, análisis detallado de cada caso y seguimiento continuo mediante consultas virtuales programadas.',
        'foto'               => $usuario->foto,
    ];

    return view('perfiles.editar', compact('perfil'));

})->middleware('auth')->name('abogado.perfil');


// GUARDAR CAMBIOS DEL PERFIL DEL ABOGADO EN LA BASE DE DATOS
Route::put('/abogado/perfil', function (Request $request) {

    abort_unless(auth()->user()->rol === 'abogado', 403);

    $usuario = auth()->user();

    $datos = $request->validate([
        'nombre'             => 'required|string|max:120',
        'especialidades'     => 'required|string|max:200',
        'email'              => 'required|email|unique:users,email,' . $usuario->id,
        'descripcion_inicio' => 'required|string|max:1000',
        'foto'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    $rutaFoto = $usuario->foto;

    if ($request->hasFile('foto')) {
        if ($rutaFoto && Storage::disk('public')->exists($rutaFoto)) {
            Storage::disk('public')->delete($rutaFoto);
        }

        $rutaFoto = $request->file('foto')->store('perfil', 'public');
    }

    $usuario->update([
        'name'               => $datos['nombre'],
        'especialidades'     => $datos['especialidades'],
        'email'              => $datos['email'],
        'descripcion_inicio' => $datos['descripcion_inicio'],
        'foto'               => $rutaFoto,
    ]);

    return redirect()->route('abogado.perfil')->with('exito', 'Tus datos, foto y descripción del inicio se guardaron en la base de datos.');

})->middleware('auth')->name('abogado.perfil.actualizar');


// SOLICITUDES
Route::get('/solicitudes/crear', [SolicitudController::class, 'create']);
Route::post('/solicitudes', [SolicitudController::class, 'store']);

Route::get('/solicitudes', function () {
    abort_unless(auth()->user()->rol === 'abogado', 403);
    return app(SolicitudController::class)->index();
})->middleware('auth');

Route::patch('/solicitudes/{solicitud}/aceptar', function (Solicitud $solicitud) {
    abort_unless(auth()->user()->rol === 'abogado', 403);
    return app(SolicitudController::class)->aceptar($solicitud);
})->middleware('auth');

Route::patch('/solicitudes/{solicitud}/rechazar', function (Solicitud $solicitud) {
    abort_unless(auth()->user()->rol === 'abogado', 403);
    return app(SolicitudController::class)->rechazar($solicitud);
})->middleware('auth');


// DETALLE DE CONSULTA DEL CLIENTE Y ABOGADO
Route::get('/consultas/cliente/{id}', function ($id) {
    abort_unless(auth()->user()->rol === 'cliente', 403);

    $solicitud = Solicitud::where('email', auth()->user()->email)
        ->findOrFail($id);

    return view('consultas.ver_cliente', compact('solicitud'));
})->middleware('auth');

Route::get('/consultas/abogado/{id}', function ($id) {
    abort_unless(auth()->user()->rol === 'abogado', 403);

    $solicitud = Solicitud::findOrFail($id);

    return view('consultas.ver_abogado', compact('solicitud'));
})->middleware('auth');


// SEGUIMIENTO DE SOLICITUDES
Route::get('/solicitudes/seguimiento/{token}', [SolicitudController::class, 'seguimiento'])
    ->name('solicitudes.seguimiento');

Route::post('/solicitudes/seguimiento/{token}/enviar-codigo', [SolicitudController::class, 'enviarCodigo'])
    ->middleware('throttle:3,10')
    ->name('solicitudes.enviarCodigo');

Route::post('/solicitudes/seguimiento/{token}/verificar-codigo', [SolicitudController::class, 'verificarCodigo'])
    ->middleware('throttle:5,10')
    ->name('solicitudes.verificarCodigo');


// TURNOS Y VIDEOLLAMADAS JITSI
Route::get('/turnos', [TurnoController::class, 'index'])
    ->middleware('auth')
    ->name('turnos.index');

Route::post('/turnos', [TurnoController::class, 'store'])
    ->middleware('auth')
    ->name('turnos.store');

Route::delete('/turnos/{turno}', [TurnoController::class, 'destroy'])
    ->middleware('auth')
    ->name('turnos.destroy');

Route::get('/turnos/{turno}/videollamada', [TurnoController::class, 'videollamada'])
    ->middleware('auth')
    ->name('turnos.videollamada');


// DISPONIBILIDAD DEL ABOGADO
Route::get('/abogado/disponibilidades', [DisponibilidadController::class, 'index'])
    ->middleware('auth')
    ->name('abogado.disponibilidades');

Route::post('/abogado/disponibilidades', [DisponibilidadController::class, 'store'])
    ->middleware('auth')
    ->name('abogado.disponibilidades.store');

Route::get('/abogado/horarios-libres-rango', function (Request $request) {
    $datos = $request->validate([
        'desde'      => ['required', 'date_format:Y-m-d'],
        'hasta'      => ['required', 'date_format:Y-m-d', 'after_or_equal:desde'],
        'abogado_id' => ['nullable', 'integer', 'exists:users,id'],
    ]);

    $abogadoId = $datos['abogado_id'] ?? (auth()->user()->rol === 'abogado' ? auth()->id() : null);

    if (!$abogadoId) {
        return response()->json(['mensaje' => 'Debe indicar el abogado.'], 422);
    }

    $cursor = \Carbon\Carbon::parse($datos['desde'])->startOfDay();
    $fin    = \Carbon\Carbon::parse($datos['hasta'])->startOfDay();
    $hoy    = now()->startOfDay();

    if ($cursor->diffInDays($fin) > 45) {
        return response()->json(['mensaje' => 'El rango máximo permitido es de 45 días.'], 422);
    }

    // 1. Consulta única de disponibilidades por user_id
    $disponibilidades = \App\Models\Disponibilidad::where('user_id', $abogadoId)
        ->get()
        ->groupBy('dia_semana');

    // 2. Consulta única de bloqueos por user_id y fecha
    $bloqueos = \App\Models\BloqueoAgenda::where('user_id', $abogadoId)
        ->whereBetween('fecha', [$cursor->format('Y-m-d'), $fin->format('Y-m-d')])
        ->get()
        ->groupBy(fn($b) => \Carbon\Carbon::parse($b->fecha)->format('Y-m-d'));

    // 3. Consulta única de turnos activos (no cancelados)
    $turnosOcupados = \App\Models\Turno::where('abogado_id', $abogadoId)
        ->where('estado', '!=', 'cancelado')
        ->whereBetween('inicio', [$cursor->copy()->startOfDay(), $fin->copy()->endOfDay()])
        ->get();

    $dias = [];

    while ($cursor->lte($fin)) {
        if ($cursor->gte($hoy)) {
            $fechaStr  = $cursor->format('Y-m-d');
            $diaSemana = $cursor->dayOfWeekIso;

            $franjas = $disponibilidades->get($diaSemana, collect());
            $bloqueosDia = $bloqueos->get($fechaStr, collect());
            $bloquesDia = [];

            foreach ($franjas as $franja) {
                $inicioBloque = \Carbon\Carbon::parse($fechaStr . ' ' . $franja->hora_inicio);
                $finFranja    = \Carbon\Carbon::parse($fechaStr . ' ' . $franja->hora_fin);

                while ($inicioBloque->copy()->addHours(2)->lte($finFranja)) {
                    $finBloque = $inicioBloque->copy()->addHours(2);

                    if ($inicioBloque->gt(now())) {
                        $estaBloqueado = $bloqueosDia->contains(function ($b) use ($fechaStr, $inicioBloque, $finBloque) {
                            if (empty($b->hora_inicio) || empty($b->hora_fin)) {
                                return true;
                            }
                            $bIni = \Carbon\Carbon::parse($fechaStr . ' ' . $b->hora_inicio);
                            $bFin = \Carbon\Carbon::parse($fechaStr . ' ' . $b->hora_fin);
                            return $inicioBloque->lt($bFin) && $finBloque->gt($bIni);
                        });

                        $estaOcupado = $turnosOcupados->contains(function ($t) use ($inicioBloque, $finBloque) {
                            $tIni = \Carbon\Carbon::parse($t->inicio);
                            $tFin = \Carbon\Carbon::parse($t->fin);
                            return $inicioBloque->lt($tFin) && $finBloque->gt($tIni);
                        });

                        if (!$estaBloqueado && !$estaOcupado) {
                            $bloquesDia[] = [
                                'hora_inicio' => $inicioBloque->format('H:i'),
                                'hora_fin'    => $finBloque->format('H:i'),
                            ];
                        }
                    }

                    $inicioBloque->addHours(2);
                }
            }

            if (!empty($bloquesDia)) {
                $dias[$fechaStr] = $bloquesDia;
            }
        }

        $cursor->addDay();
    }

    return response()->json([
        'desde' => $datos['desde'],
        'hasta' => $datos['hasta'],
        'dias'  => $dias,
    ]);
})->middleware('auth')->name('abogado.horarios-libres-rango');


Route::delete('/abogado/disponibilidades/{id}', [DisponibilidadController::class, 'destroy'])
    ->middleware('auth')
    ->name('abogado.disponibilidades.destroy');


// BLOQUEOS DE AGENDA
Route::post('/abogado/bloqueos', [BloqueoAgendaController::class, 'store'])
    ->middleware('auth')
    ->name('abogado.bloqueos.store');

Route::get('/abogado/bloqueos', [BloqueoAgendaController::class, 'index'])
    ->middleware('auth')
    ->name('abogado.bloqueos');

Route::delete('/abogado/bloqueos/{id}', [BloqueoAgendaController::class, 'destroy'])
    ->middleware('auth')
    ->name('abogado.bloqueos.destroy');