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

                @if (
                    session('solicitud_verificada') ===
                    $solicitud->token_seguimiento
                    && $solicitud->email_verificado !== null
                )

                    <p>
                        El abogado aceptó tu solicitud.
                        Ya podés crear tu cuenta para acceder a tu perfil,
                        consultar tus casos y reservar turnos.
                    </p>

                    <div>
                        <a href="{{ route('registro.mostrar') }}" class="boton">
                            Crear mi cuenta
                        </a>
                    </div>

                @else

                    <p>
                        El abogado aceptó tu solicitud.
                        Para crear tu cuenta, ingresá desde el enlace
                        que enviamos a tu correo electrónico.
                    </p>

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
                <a href="/" class="boton">
                    Volver al inicio
                </a>
            </div>

        </div>
    </main>

</body>
</html>
