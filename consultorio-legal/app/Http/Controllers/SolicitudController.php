<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\User; //comprueba si el correo ya tiene una cuenta
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter; //limita el envio de codigos

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

    // Generar y enviar el código de verificación
    public function enviarCodigo(string $token)
    {
        // Buscar la solicitud mediante su token privado
        $solicitud = Solicitud::where('token_seguimiento', $token)
            ->firstOrFail();

        // Solo las solicitudes aceptadas pueden verificar su correo
        if ($solicitud->estado !== 'aceptada') {
            abort(403, 'La solicitud todavía no fue aceptada.');
        }

        // Si ya existe una cuenta, no generar otro código
        if (User::where('email', $solicitud->email)->exists()) {
            return redirect()->route('login')->with(
                'exito',
                'Este correo ya tiene una cuenta. Iniciá sesión.'
            );
        }

        // Limitar los envíos por solicitud y por correo
        $claveSolicitud = 'codigo-solicitud:' . $solicitud->id;
        $claveCorreo = 'codigo-email:' . hash('sha256', strtolower($solicitud->email));

        if (
            RateLimiter::tooManyAttempts($claveSolicitud, 3) ||
            RateLimiter::tooManyAttempts($claveCorreo, 5)
        ) {
            return back()->withErrors([
                'codigo' => 'Solicitaste demasiados códigos. Esperá unos minutos antes de volver a intentar.'
            ]);
        }

        // Contar los envíos durante una hora
        RateLimiter::hit($claveSolicitud, 3600);
        RateLimiter::hit($claveCorreo, 3600);

        // Generar un código aleatorio de 6 dígitos
        $codigo = (string) random_int(100000, 999999);

        // Guardar el código protegido y su vencimiento
        $solicitud->codigo_verificacion = Hash::make($codigo);
        $solicitud->codigo_expira = now()->addMinutes(10);

        // Reiniciar los intentos para el nuevo código
        $solicitud->intentos_verificacion = 0;

        $solicitud->save();

        // Enviar el código al correo de la solicitud
        Mail::raw(
            "Tu código de verificación para Consultorio Legal es: $codigo\n\n" .
            "Este código vence en 10 minutos.",
            function ($mensaje) use ($solicitud) {
                $mensaje->to($solicitud->email)
                    ->subject('Código de verificación - Consultorio Legal');
            }
        );

        return redirect()->route(
            'solicitudes.seguimiento',
            ['token' => $token]
        )->with(
            'exito',
            'Se generó tu código de verificación. Revisá tu correo.'
        );
    }

    // Verificar el código ingresado por el cliente
    public function verificarCodigo(Request $request, string $token)
    {
        // Comprobar que se ingresaron exactamente 6 números
        $datos = $request->validate([
            'codigo' => 'required|digits:6',
        ]);

        // Transacción para evitar verificaciones simultáneas
        $resultado = DB::transaction(function () use ($datos, $token) {

            $solicitud = Solicitud::where('token_seguimiento', $token)
                ->lockForUpdate()
                ->firstOrFail();

            // La solicitud debe estar aceptada
            if ($solicitud->estado !== 'aceptada') {
                abort(403, 'La solicitud no está aceptada.');
            }

            // Si ya existe una cuenta, no permitir otra
            if (User::where('email', $solicitud->email)->exists()) {
                return [
                    'error' => 'Este correo ya tiene una cuenta. Iniciá sesión.'
                ];
            }

            // Comprobar si alcanzó el máximo de intentos
            if ($solicitud->intentos_verificacion >= 5) {
                return [
                    'error' => 'Superaste los 5 intentos permitidos. Solicitá un código nuevo.'
                ];
            }

            // Comprobar que exista un código y que no haya vencido
            if (
                !$solicitud->codigo_verificacion ||
                !$solicitud->codigo_expira ||
                now()->greaterThan($solicitud->codigo_expira)
            ) {
                return [
                    'error' => 'El código venció o no existe. Solicitá uno nuevo.'
                ];
            }

            // Comparar el código ingresado con el hash de Aiven
            if (!Hash::check($datos['codigo'], $solicitud->codigo_verificacion)) {

                // Sumar un intento fallido
                $solicitud->intentos_verificacion++;

                // Si llegó a 5 intentos, invalidar el código
                if ($solicitud->intentos_verificacion >= 5) {
                    $solicitud->codigo_verificacion = null;
                    $solicitud->codigo_expira = null;

                    $solicitud->save();

                    return [
                        'error' => 'Superaste los 5 intentos permitidos. Solicitá un código nuevo.'
                    ];
                }

                $solicitud->save();

                return [
                    'error' => 'El código ingresado es incorrecto. Intentá nuevamente.'
                ];
            }

            // Marcar el correo como verificado
            $solicitud->email_verificado = now();

            // Invalidar el código para que no se reutilice
            $solicitud->codigo_verificacion = null;
            $solicitud->codigo_expira = null;

            // Reiniciar el contador
            $solicitud->intentos_verificacion = 0;

            $solicitud->save();

            return [
                'verificado' => true,
                'exito' => '¡Correo verificado correctamente! Ya podés continuar con el registro.'
            ];
        });

        // Mostrar un error si el código es incorrecto
        if (isset($resultado['error'])) {
            return back()->withErrors([
                'codigo' => $resultado['error']
            ]);
        }

        // Guardar en sesión la solicitud que verificó el cliente
        if (!empty($resultado['verificado'])) {
            $request->session()->regenerate();

            $request->session()->put(
                'solicitud_verificada',
                $token
            );
        }

        return back()->with(
            'exito',
            $resultado['exito']
        );
    }

    // Aceptar una solicitud
    public function aceptar(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'aceptada']);

        return back();
    }

    // Rechazar una solicitud
    public function rechazar(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => 'rechazada']);

        return back();
    }
}
