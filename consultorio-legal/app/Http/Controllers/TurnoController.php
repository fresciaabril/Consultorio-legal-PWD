<?php

namespace App\Http\Controllers;

use App\Models\Turno;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TurnoController extends Controller
{
    // Mostrar los turnos del usuario autenticado
    public function index(Request $request)
    {
        $usuario = $request->user(); //obtiene el usuario que inició sesión 

        if ($usuario->rol === 'abogado') { //comprueba que es el abogado

            // El abogado ve los turnos asignados a él
            $turnos = Turno::with('cliente')->where('abogado_id', $usuario->id)->orderBy('inicio')->get();

        } elseif ($usuario->rol === 'cliente') { //si es cliente

            // El cliente ve únicamente sus turnos
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
    public function destroy(Request $request, Turno $turno)
    {
        $usuario = $request->user();

        // Verificar que sea el abogado asignado al turno
        if (
            $usuario->rol !== 'abogado' ||
            (int) $turno->abogado_id !== (int) $usuario->id
        ) {
            abort(403, 'No tenés permiso para cancelar este turno.');
        }

        // Verificar que el turno esté reservado
        if ($turno->estado !== 'reservado') {
            return response()->json([
                'mensaje' => 'Este turno no se puede cancelar.'
            ], 422);
        }

        // No permitir cancelar turnos que ya comenzaron
        if ($turno->inicio->isPast()) {
            return response()->json([
                'mensaje' => 'No se puede cancelar un turno que ya comenzó.'
            ], 422);
        }

        // Cambiar el estado sin eliminar el registro
        $turno->estado = 'cancelado';
        $turno->save();

        return response()->json([
            'mensaje' => 'Turno cancelado correctamente.'
        ]);
    }


    // Reservar un turno
    public function store(Request $request)
    {
        $usuario = $request->user();

        // Solo los clientes pueden reservar
        if ($usuario->rol !== 'cliente') {
            abort(403, 'Solo los clientes pueden reservar turnos.');
        }

        // Validar los datos recibidos del formulario
        $datos = $request->validate([
            'abogado_id' => 'required|integer|exists:users,id',
            'inicio' => 'required|date|after:now',
            'modalidad' => 'required|in:virtual,presencial',
            'motivo' => 'nullable|string|max:1000',
        ]);

        // Verificar que el usuario seleccionado sea abogado
        $abogado = User::findOrFail($datos['abogado_id']);

        if ($abogado->rol !== 'abogado') {
            throw ValidationException::withMessages([
                'abogado_id' => 'El usuario seleccionado no es un abogado.'
            ]);
        }

        // Calcular las dos horas de duración
        $inicio = Carbon::parse($datos['inicio']);
        $fin = $inicio->copy()->addHours(2);

        // PENDIENTE: integrar la disponibilidad de Male.
        // Antes de guardar, debemos comprobar:
        // - Que el inicio sea un bloque fijo habilitado.
        // - Que esté dentro del horario laboral del abogado.
        // - Que no exista un bloqueo de agenda.
        // - Que no haya otro turno reservado superpuesto.
        // - Que estas verificaciones y la reserva sean atómicas.
        //
        // Por seguridad, todavía NO guardamos el turno.

        return response()->json([
            'mensaje' => 'La reserva todavía no está habilitada: falta integrar la disponibilidad.',
            'inicio' => $inicio->toDateTimeString(),
            'fin' => $fin->toDateTimeString(),
            'modalidad' => $datos['modalidad'],
        ], 503);
    }

}
