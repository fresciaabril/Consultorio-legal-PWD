<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use App\Models\User;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class TurnoController extends Controller
{
    // Mostrar los turnos del usuario autenticado
    public function index(Request $request){
        $usuario = $request->user(); //obtiene el usuario que inició sesión 
        if ($usuario->rol === 'abogado') { 
            // El abogado ve los turnos asignados
            $turnos = Turno::with('cliente')->where('abogado_id', $usuario->id)->orderBy('inicio')->get();
        } elseif ($usuario->rol === 'cliente') {
            $turnos = Turno::with('abogado')
                ->where('cliente_id', $usuario->id)
                ->orderBy('inicio')
                ->get();
        } else {
            abort(403, 'No tenés permiso para consultar turnos.');
        }
        return response()->json($turnos); //Ejecuta la consulta y devuelve una colección de turnos.
    }


    // Cancelar un turno (solo el abogado)
    public function destroy(Request $request, Turno $turno){
        $usuario = $request->user();
        if ( $usuario->rol !== 'abogado' || (int) $turno->abogado_id !== (int) $usuario->id){
            abort(403, 'No tenés permiso para cancelar este turno.');
        }
        if ($turno->estado !== 'reservado') {
            return response()->json([ 'mensaje' => 'Este turno no se puede cancelar.'], 422);
        }

        // No permitir cancelar turnos que ya comenzaron
        if ($turno->inicio->isPast()) {
            return response()->json(['mensaje' => 'No se puede cancelar un turno que ya comenzó.'], 422);
        }
        $turno->estado = 'cancelado';
        $turno->save();
        return response()->json(['mensaje' => 'Turno cancelado correctamente.']);
    }


    // Reservar un turno
    public function store(Request $request, AgendaService $agendaService){
        $usuario = $request->user();
        if ($usuario->rol !== 'cliente') {
            abort(403, 'Solo los clientes pueden reservar turnos.');
        }
        $datos = $request->validate([
            'abogado_id' => 'required|integer|exists:users,id',
            'inicio' => 'required|date|after:now',
            'modalidad' => 'required|in:virtual,presencial',
            'motivo' => 'nullable|string|max:1000',
        ]);

        // Verificar que el usuario seleccionado sea abogado
        $abogado = User::findOrFail($datos['abogado_id']);
        if ($abogado->rol !== 'abogado') {
            throw ValidationException::withMessages(['abogado_id' => 'El usuario seleccionado no es un abogado.']);
        }

        // Calcular las dos horas de duración
        $inicio = Carbon::parse($datos['inicio']);
        $fin = $inicio->copy()->addHours(2);
        $fecha = $inicio->format('Y-m-d');
        $horaInicio = $inicio->format('H:i');

        // Guardado que evita reservar simultáneas en el mismo horario
        $turno = DB::transaction(function () use ($usuario, $abogado, $datos, $inicio, $fin, $fecha, $horaInicio, $agendaService) {
            // Bloqueamos los turnos activos del abogado para ese día 
            Turno::where('abogado_id', $abogado->id)
                ->where('estado', 'reservado')
                ->whereDate('inicio', $fecha)
                ->lockForUpdate()
                ->get();

            // verificamos que este disponible 
            $horariosLibres = $agendaService->obtenerHorariosLibres($abogado->id, $fecha, 120);
            $esBloqueValido = collect($horariosLibres)->contains(function ($bloque) use ($horaInicio) {
                return $bloque['hora_inicio'] === $horaInicio;
            });

            if (!$esBloqueValido) {
                throw ValidationException::withMessages([
                    'inicio' => 'El horario seleccionado no está disponible o ya fue reservado.'
                ]);
            }
            $existeSuperposicion = Turno::where('abogado_id', $abogado->id)
                ->where('estado', 'reservado')
                ->where('inicio', '<', $fin)
                ->where('fin', '>', $inicio)
                ->exists();
            if ($existeSuperposicion) {
                throw ValidationException::withMessages([
                    'inicio' => 'Ese horario acaba de ser reservado por otro cliente.'
                ]);
            }

            // Creamos y guardamos el turno en MySQL
            return Turno::create([
                'cliente_id' => $usuario->id,
                'abogado_id' => $abogado->id,
                'inicio' => $inicio,
                'fin' => $fin,
                'modalidad' => $datos['modalidad'],
                'estado' => 'reservado',
                'motivo' => $datos['motivo'] ?? null,
            ]);
        });

        return response()->json([
            'mensaje' => 'Turno reservado correctamente.',
            'turno' => $turno,
        ], 201);
    }

    // Acceder a la sala de videollamada vinculada al turno
    public function videollamada(Turno $turno)
    {
        $usuario = auth()->user();

        // 1. Control de acceso: solo el cliente dueño del turno o el abogado asignado
        $esCliente = $usuario->rol === 'cliente' && (int) $turno->cliente_id === (int) $usuario->id;
        $esAbogado = $usuario->rol === 'abogado' && (int) $turno->abogado_id === (int) $usuario->id;

        abort_unless($esCliente || $esAbogado, 403, 'No tenés permiso para acceder a esta videollamada.');

        // 2. Validar modalidad y estado
        abort_if($turno->modalidad !== 'virtual', 403, 'Este turno es presencial y no tiene sala virtual.');
        abort_if($turno->estado === 'cancelado', 403, 'Este turno fue cancelado.');

        $turno->load(['cliente', 'abogado']);

        return view('consultas.videollamada_turno', compact('turno'));
    }
}