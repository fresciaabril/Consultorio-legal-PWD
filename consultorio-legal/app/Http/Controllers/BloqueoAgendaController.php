<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloqueoAgenda;

class BloqueoAgendaController extends Controller
{
    public function store(Request $request){
        //solo el abogado puede bloquear su agenda
        abort_unless(auth()->user()->rol === 'abogado', 403);

        //validamos los datos recibidos
        $datos = $request->validate([
            'fecha' => 'required|date_format:Y-m-d',
            'hora_inicio' => 'nullable|required_with:hora_fin|date_format:H:i',
            'hora_fin' => 'nullable|required_with:hora_inicio|date_format:H:i|after:hora_inicio',
            'motivo' => 'nullable|string|max:255',
        ]);

        //buscamos bloqueos existentes para esas fechas
        $bloqueos = BloqueoAgenda::where('user_id', auth()->id())
        ->where('fecha', $datos['fecha'])
        ->get();

        foreach($bloqueos as $bloqueo){
            //si ya esta bloqueado todo el dia
            if($bloqueo->hora_inicio === null && $bloqueo->hora_fin === null){
                return response()->json([
                    'mensaje' => 'Esta fecha ya esta bloqueada completamente.'
                ], 422);
            }
            //comprobamos si los horarios se super ponen 
            if($datos['hora_inicio'] < $bloqueo->hora_fin && $datos['hora_fin'] > $bloque->hora_inicio){
                return response()->json([
                    'mensaje' => 'Este horario se superpone con otro bloqueo.'
                ], 422);
            }
        }

        //guardamos el bloqueo de agenda del abogado
        $bloqueo = BloqueoAgenda::create([
            'user_id' => auth()->id(),
            'fecha' => $datos['fecha'],
            'hora_inicio' => $datos['hora_inicio'] ?? null,
            'hora_fin' => $datos['hora_fin'] ?? null,
            'motivo' => $datos['motivo'] ?? null,
        ]);

        return response()->json([
            'mensaje' => 'Bloqueo de agenda guardado correctamente.',
            'bloqueo' => $bloqueo
        ], 201);
    }
}
