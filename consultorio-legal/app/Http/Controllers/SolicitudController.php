<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function create(){
        return view('solicitudes.crear');
    }

    public function store(Request $request){
        $datos = $request->validate([
            'nombre'      => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'telefono'    => 'nullable|string|max:50',
            'area'        => 'required|in:familia,laboral,civil,otro',
            'descripcion' => 'required|string',
        ]);

        Solicitud::create($datos);

        return redirect('/solicitudes/crear')
        ->with('exito', 'Tu solicitud fue enviada correctamente.');
    }


    public function index(){
        $solicitudes = Solicitud::latest()->get();
        return view('solicitudes.estado', compact('solicitudes'));
    }

     public function aceptar(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'aceptada']);
        return back();
    }

    public function rechazar(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'rechazada']);
        return back();
    }
}
 