<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController{

    // Mostrar la pantalla de login-devuelve vista login
    public function mostrarLogin(){
        return view('login');
    }

    // Iniciar sesión
    public function iniciarSesion(Request $request){
        // Validar los datos ingresados
        $datos = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar iniciar sesión
        if (Auth::attempt($datos)) { //comprueba en la base de datos

            // Regenerar la sesión por seguridad
            $request->session()->regenerate();

            // Obtener el usuario que inició sesión
            $usuario = Auth::user();

            // Redirigir según su rol
            if ($usuario->rol === 'abogado') {
                return redirect()->route('abogado');
            }

            return redirect()->route('perfil');
        }

        // Si los datos son incorrectos
        return back()->withErrors([
            'email' => 'El email o la contraseña son incorrectos.',
        ])->onlyInput('email');
    }

    // Cerrar sesión
    public function cerrarSesion(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
