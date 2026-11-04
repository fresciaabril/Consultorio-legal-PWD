<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de solicitud</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f3ef;
            color: #263238;
        }

        header {
            background-color: #183746;
            color: white;
            padding: 25px 8%;
        }

        header h1 {
            margin: 0;
        }

        main {
            max-width: 650px;
            margin: 70px auto;
            padding: 0 20px;
        }

        .card {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.07);
            text-align: center;
        }

        h2 {
            color: #183746;
        }

        .estado {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }

        .pendiente {
            background-color: #fff3cd;
            color: #856404;
        }

        .aceptada {
            background-color: #d4edda;
            color: #155724;
        }

        .rechazada {
            background-color: #f8d7da;
            color: #721c24;
        }

        .boton {
            display: inline-block;
            background-color: #183746;
            color: white;
            padding: 13px 25px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 20px;
        }

        .boton:hover {
            background-color: #28596b;
        }

        p {
            line-height: 1.6;
        }
    </style>
</head>

<body>

    <header>
        <h1>Estudio Jurídico</h1>
    </header>

    <main>
        <div class="card">

            <h2>Seguimiento de tu solicitud</h2>

            @if ($solicitud->estado === 'pendiente')

                <div class="estado pendiente">
                    Solicitud pendiente
                </div>

                <p>
                    Recibimos tu consulta correctamente.
                    El abogado todavía está revisando tu solicitud.
                </p>

                <p>
                    Podés volver a consultar esta página más adelante.
                </p>

            @elseif ($solicitud->estado === 'aceptada')

                <div class="estado aceptada">
                    ¡Solicitud aceptada!
                </div>

                @if (session('exito'))
                    <p style="color: #137333;">
                        {{ session('exito') }}
                    </p>
                @endif

                @if ($errors->any())
                    <p style="color: #c5221f;">
                        {{ $errors->first() }}
                    </p>
                @endif

                @if (
                    session('solicitud_verificada') ===
                    $solicitud->token_seguimiento
                    && $solicitud->email_verificado !== null
                )

                    <!-- CORREO VERIFICADO Y SESIÓN ACTIVA -->

                    <p style="color: #137333;">
                        ¡Tu correo electrónico fue verificado correctamente!
                    </p>

                    <p>
                        Ya podés continuar con la creación
                        de tu perfil de cliente.
                    </p>

                    <a href="{{ route('registro.mostrar') }}" class="boton">
                        Crear mi perfil
                    </a>

                @else

                    <!-- CORREO SIN VERIFICAR O SESIÓN PERDIDA -->

                    @if ($solicitud->email_verificado === null)

                        <p>
                            El abogado aceptó tu solicitud.
                            Para continuar con el registro,
                            primero necesitamos verificar
                            tu correo electrónico.
                        </p>

                    @else

                        <p>
                            Tu correo ya fue verificado anteriormente,
                            pero necesitamos confirmar nuevamente
                            que sos su propietario antes de crear tu perfil.
                        </p>

                    @endif

                    <!-- Solicitar un código -->
                    <form
                        action="{{ route('solicitudes.enviarCodigo', ['token' => $solicitud->token_seguimiento]) }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="boton"
                            style="border: none; cursor: pointer;"
                        >
                            Enviar código de verificación
                        </button>
                    </form>

                    <!-- Ingresar el código -->
                    <form
                        action="{{ route('solicitudes.verificarCodigo', ['token' => $solicitud->token_seguimiento]) }}"
                        method="POST"
                        style="margin-top: 25px;"
                    >
                        @csrf

                        <label for="codigo">
                            Ingresá el código de 6 dígitos:
                        </label>

                        <div style="margin-top: 12px;">
                            <input
                                type="text"
                                id="codigo"
                                name="codigo"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                required
                                style="padding: 12px; border: 1px solid #ccc;
                                border-radius: 6px; font-size: 16px;"
                            >
                        </div>

                        <button
                            type="submit"
                            class="boton"
                            style="border: none; cursor: pointer;"
                        >
                            Verificar código
                        </button>
                    </form>

                @endif

            @elseif ($solicitud->estado === 'rechazada')

                <div class="estado rechazada">
                    Solicitud rechazada
                </div>

                <p>
                    Por el momento, el abogado no podrá tomar tu consulta.
                </p>

            @else

                <p>
                    El estado de esta solicitud no está disponible.
                </p>

            @endif

            <div>
                <button
                    type="button"
                    class="boton"
                    onclick="copiarEnlace()"
                    style="border: none; cursor: pointer;"
                >
                    Copiar enlace de seguimiento
                </button>
            </div>

            <script>
                async function copiarEnlace() {
                    try {
                        await navigator.clipboard.writeText(window.location.href);
                        alert('¡Enlace copiado! Guardalo para consultar tu solicitud.');
                    } catch (error) {
                        alert('No se pudo copiar el enlace automáticamente.');
                    }
                }
            </script>

            <div>
                <a href="/" class="boton">
                    Volver al inicio
                </a>
            </div>

        </div>
    </main>

</body>
</html>
