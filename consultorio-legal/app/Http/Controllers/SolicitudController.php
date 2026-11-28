<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\User; //comprueba si el correo ya tiene una cuenta
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SolicitudController extends Controller
{
    // Mostrar formulario de solicitud
    public function create()
    {
        return view('solicitudes.crear');
    }

    // Guardar una nueva solicitud
    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'area' => 'required|in:familia,laboral,civil,otro',
            'descripcion' => 'required|string',
        ]);

        // Generamos un token único para el seguimiento
        $datos['token_seguimiento'] = Str::random(64);

        // Guardamos la solicitud en Aiven
        $solicitud = Solicitud::create($datos);
        //Recargamos la solicitud con su stado inicial
        $solicitud->refresh();

        //Enviamos el correo de confirmacion
        $this->enviarCorreoEstado($solicitud);

        // Redirigimos al seguimiento de esta solicitud
        return redirect()->route(
            'solicitudes.seguimiento',
            ['token' => $solicitud->token_seguimiento]
        );
    }

    // Listar solicitudes para el abogado
    public function index()
    {
        $solicitudes = Solicitud::latest()->get();

        return view('solicitudes.estado', compact('solicitudes'));
    }

    // Mostrar seguimiento de una solicitud
    public function seguimiento(string $token)
    {
        $solicitud = Solicitud::where(
            'token_seguimiento',
            $token
        )->firstOrFail();

        return view('solicitudes.seguimiento', compact('solicitud'));
    }

    public function habilitarRegistro(Request $request, string $token)
    {
        // Buscar la solicitud mediante su token
        $solicitud = Solicitud::where('token_seguimiento', $token)
            ->firstOrFail();

        // Comprobar que la solicitud esté aceptada
        if ($solicitud->estado !== 'aceptada') {
            abort(403, 'La solicitud no está aceptada.');
        }

        // Comprobar que el cliente no tenga una cuenta
        if (User::where('email', $solicitud->email)->exists()) {
            return redirect()->route('login')
                ->with('exito', 'Este correo ya tiene una cuenta.');
        }

        // Marcar el correo como verificado
        $solicitud->email_verificado = now();
        $solicitud->save();

        // Guardar el permiso de registro en la sesión
        $request->session()->regenerate();
        $request->session()->put('solicitud_verificada', $token);

        // Volver a la pantalla de seguimiento
        return redirect()->route('solicitudes.seguimiento', [
            'token' => $token
        ]);
    }


    // Aceptar una solicitud
    public function aceptar(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'aceptada']);

        //enviar coreo de aceptacion
        $this->enviarCorreoEstado($solicitud);

        return back();
    }

    // Rechazar una solicitud
    public function rechazar(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'rechazada']);

        $this->enviarCorreoEstado($solicitud);

        return back();
    }

    private function enviarCorreoEstado(Solicitud $solicitud)
    {
        $asunto = match ($solicitud->estado) {
            'pendiente' => 'Solicitud recibida - Consultorio Legal',
            'aceptada' => 'Tu solicitud fue aceptada - Consultorio Legal',
            'rechazada' => 'Estado de tu solicitud - Consultorio Legal',
            default => 'Consultorio Legal',
        };

        // Generamos el enlace de acuerdo al estado
        if ($solicitud->estado === 'aceptada') {
            $enlace = URL::temporarySignedRoute(
                'solicitudes.habilitarRegistro',
                now()->addDays(7),
                ['token' => $solicitud->token_seguimiento]
            );
        } else {
            $enlace = route('solicitudes.seguimiento', [
                'token' => $solicitud->token_seguimiento
            ]);
        }

        try {
            Mail::send(
                'emails.estado_solicitud',
                [
                    'solicitud' => $solicitud,
                    'enlace' => $enlace
                ],
                function ($mensaje) use ($solicitud, $asunto) {
                    $mensaje->to($solicitud->email)
                        ->subject($asunto);
                }
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error(
                'No se pudo enviar el correo de la solicitud ID: ' . $solicitud->id
            );
        }
    }
}
