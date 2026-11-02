<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultorioController extends Controller
{
    // Muestra el perfil del cliente con solicitudes simuladas
    public function cliente()
    {
        $solicitudes = collect([
            (object)[
                'id' => 1,
                'area' => 'Derecho Civil',
                'descripcion' => 'Consulta sobre contrato de alquiler.',
                'estado' => 'aceptada'
            ],
            (object)[
                'id' => 2,
                'area' => 'Derecho Laboral',
                'descripcion' => 'Duda sobre liquidación final.',
                'estado' => 'pendiente'
            ]
        ]);

        return view('perfiles.cliente', compact('solicitudes'));
    }

    // Muestra el panel del abogado con solicitudes simuladas
    public function abogado()
    {
        $solicitudes = collect([
            (object)[
                'id' => 1,
                'nombre' => 'Juan Pérez',
                'email' => 'juan@example.com',
                'area' => 'Derecho Civil',
                'descripcion' => 'Consulta sobre contrato de alquiler.',
                'estado' => 'aceptada'
            ],
            (object)[
                'id' => 2,
                'nombre' => 'María Gómez',
                'email' => 'maria@example.com',
                'area' => 'Derecho Familia',
                'descripcion' => 'Trámite de cuota alimentaria.',
                'estado' => 'pendiente'
            ]
        ]);

        return view('perfiles.abogado', compact('solicitudes'));
    }
}
