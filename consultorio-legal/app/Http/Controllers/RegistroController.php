<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegistroController extends Controller
{
    // Mostrar el formulario de registro
    public function mostrarRegistro(Request $request)
    {
        // Obtener el token de la solicitud verificada
        $token = $request->session()->get('solicitud_verificada');

        // Si no verificó el correo, no puede registrarse
        if (!$token) {
            abort(403, 'Primero tenés que verificar tu correo electrónico.');
        }

        // Buscar la solicitud en Aiven
        $solicitud = Solicitud::where('token_seguimiento', $token)
            ->firstOrFail();

        // Comprobar que esté aceptada y verificada
        if (
            $solicitud->estado !== 'aceptada' ||
            $solicitud->email_verificado === null
        ) {
            abort(403, 'La solicitud no está habilitada para registrarse.');
        }

        // Evitar registrar un correo que ya tiene cuenta
        if (User::where('email', $solicitud->email)->exists()) {
            return redirect()->route('login')
                ->with('exito', 'Este correo ya tiene una cuenta. Iniciá sesión.');
        }

        // Mostrar el formulario que ya creamos
        return view('auth.registro', compact('solicitud'));
    }

    // Guardar el nuevo cliente en Aiven
    public function guardarRegistro(Request $request)
    {
        // Obtener la solicitud verificada desde la sesión
        $token = $request->session()->get('solicitud_verificada');

        if (!$token) {
            abort(403, 'Primero tenés que verificar tu correo electrónico.');
        }

        // Validar la contraseña
        $datos = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Crear el usuario dentro de una transacción
        $usuario = DB::transaction(function () use ($token, $datos) {

            $solicitud = Solicitud::where('token_seguimiento', $token)
                ->lockForUpdate()
                ->firstOrFail();

            // Comprobar nuevamente los permisos antes de guardar
            if (
                $solicitud->estado !== 'aceptada' ||
                $solicitud->email_verificado === null
            ) {
                abort(403, 'La solicitud no está habilitada para registrarse.');
            }

            // Evitar cuentas duplicadas
            if (User::where('email', $solicitud->email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Este correo ya tiene una cuenta. Iniciá sesión.'
                ]);
            }

            // Crear el cliente con los datos de la solicitud
            $usuario = new User();
            $usuario->name = $solicitud->nombre;
            $usuario->email = $solicitud->email;
            $usuario->password = Hash::make($datos['password']);
            $usuario->rol = 'cliente';
            $usuario->email_verified_at = now();
            $usuario->save();

            return $usuario;
        });

        // Eliminar el permiso temporal de registro
        $request->session()->forget('solicitud_verificada');

        // Iniciar sesión automáticamente
        Auth::login($usuario);

        // Renovar la sesión por seguridad
        $request->session()->regenerate();

        // Redirigir al perfil del cliente
        return redirect()->route('perfil');
    }
}
