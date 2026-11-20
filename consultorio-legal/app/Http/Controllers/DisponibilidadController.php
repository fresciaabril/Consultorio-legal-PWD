<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disponibilidad;
use App\Services\AgendaService;

class DisponibilidadController extends Controller
{
    public function index(){
        //verificamos que sea un abogado
        abort_unless(auth()->user()->rol === 'abogado', 403);

        //obtenemos el ID del abogado autenticado
        $abogadoId = auth()->id();

        //buscamos sus horarios de atencion
        $disponibilidades = Disponibilidad::where('user_id', $abogadoId)
        ->orderBy('dia_semana')
        ->orderBy('hora_inicio')
        ->get();

        //devolvemos los horarios en formato JSON
        return response()->json($disponibilidades);
    }

    public function store(Request $request){
        //solo el abogado puede configurar su disponibilidad
        abort_unless(auth()->user()->rol === 'abogado', 403);

        //validamos  los datos recibidos
        $datos = $request->validate([
            'dia_semana' => 'required|integer|between:1,7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        //comprobamos si ya existe un horario que se superponga
        $superposicon = Disponibilidad::where('user_id', auth()->id())
        ->where('dia_semana', $datos['dia_semana'])
        ->where('hora_inicio', '<', $datos['hora_fin'])
        ->where('hora_fin', '>', $datos['hora_inicio'])
        ->exists();

        if($superposicon){
            return response()->json([
                'mensaje' => 'Este horario se superpone con otro ya registrado.'], 422);
        }

        //guardamos la disponibilidad del abogado
        $disponibilidad = Disponibilidad::create([
            'user_id' => auth()->id(),
            'dia_semana' => $datos['dia_semana'],
            'hora_inicio' => $datos['hora_inicio'],
            'hora_fin' => $datos['hora_fin'],
        ]);

        //devolvemos una respuesta de exito
        return response()->json([
            'mensaje' => 'Disponibilidad guardada correctamente.',
            'disponibilidad' => $disponibilidad], 201);
    }


    public function horariosLibres(Request $request, AgendaService $agendaService)
    {
        $datos = $request->validate([
            'fecha' => 'required|date_format:Y-m-d|after_or_equal:today',
        ]);

        abort_unless(auth()->user()->rol === 'abogado', 403);

        $horarios = $agendaService->obtenerHorariosLibres(
            auth()->id(),
            $datos['fecha']
        );

        return response()->json([
            'fecha' => $datos['fecha'],
            'horarios_libres' => $horarios,
        ]);
    }

    public function configurar()
    {
        abort_unless(auth()->user()->rol === 'abogado', 403);

        return view('agenda.configurar');
    }

    public function destroy($id)
    {
        abort_unless(auth()->user()->rol === 'abogado', 403);

        $disponibilidad = Disponibilidad::where('user_id', auth()->id())
            ->findOrFail($id);

        $disponibilidad->delete();

        return response()->json([
            'mensaje' => 'Disponibilidad eliminada correctamente.'
        ]);
    }
}
  