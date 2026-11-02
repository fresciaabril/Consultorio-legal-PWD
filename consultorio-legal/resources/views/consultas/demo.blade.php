<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta virtual | Estudio Jurídico</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f3ef;
            color: #222;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #1a3b47;
            color: white;
            padding: 20px;
            text-align: center;
        }

        main {
            max-width: 1000px;
            width: 100%;
            margin: 30px auto;
            padding: 20px;
            flex-grow: 1;
        }

        .informacion {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .informacion h2 {
            color: #172b4d;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .estado-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .estado-aceptada { background-color: #e6f4ea; color: #137333; }
        .estado-pendiente { background-color: #fef7e0; color: #b06000; }
        .estado-rechazada { background-color: #fce8e6; color: #c5221f; }

        #jitsi-container {
            width: 100%;
            min-height: 600px;
            background-color: #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 15px;
        }

        #mensaje {
            margin-bottom: 15px;
        }

        button#iniciar {
            padding: 12px 20px;
            background-color: #1a3b47 ;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
            font-weight: bold;
        }

        /* Botones de acción para estado pendiente */
        .btn-accion {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }

        .btn-aceptar {
            background-color: #28a745;
            color: white;
        }

        .btn-rechazar {
            background-color: #dc3545;
            color: white;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #777777;
            margin-top: auto;
        }

        .btn-volver {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .btn-volver:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.6);
            transform: translateY(-1px);
        }
    </style>
</head>

<body>

    <header>
        <h1>Estudio Jurídico</h1>
        <a href="javascript:window.close()" class="btn-volver">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            Cerrar ventana
        </a>
    </header>

    <main>
        <section class="informacion">
            <h2>Detalle de la Consulta #{{ $solicitud->id ?? $id ?? 1 }}</h2>

            <p style="margin-bottom: 8px;">
                <strong>Cliente:</strong> {{ $solicitud->cliente ?? 'Cliente de prueba' }}
            </p>
            <p style="margin-bottom: 8px;">
                <strong>Área:</strong> {{ $solicitud->area ?? 'Derecho General' }}
            </p>
            <p style="margin-bottom: 8px;">
                <strong>Estado:</strong> 
                <span class="estado-tag estado-{{ strtolower($solicitud->estado ?? 'pendiente') }}">
                    {{ $solicitud->estado ?? 'pendiente' }}
                </span>
            </p>

            <p style="margin-top: 15px;">
                <strong>Descripción del caso:</strong>
            </p>
            <p style="background: #fafafa; padding: 12px; border: 1px solid #eee; border-radius: 4px; margin-top: 5px;">
                {{ $solicitud->descripcion ?? 'Sin descripción disponible.' }}
            </p>
        </section>

        {{-- Si la consulta está ACEPTADA: muestra la videollamada --}}
        @if (isset($solicitud->estado) && $solicitud->estado === 'aceptada')
            <section class="informacion">
                <h2>Reunión en Vivo</h2>

                <p id="mensaje">
                    Presioná el botón para iniciar la consulta.
                </p>

                <button id="iniciar">
                    Iniciar videollamada
                </button>

                <div id="jitsi-container"></div>
            </section>

            <script src="https://meet.jit.si/external_api.js"></script>
            <script>
                let api = null;

                const boton = document.getElementById('iniciar');
                const mensaje = document.getElementById('mensaje');
                const contenedor = document.getElementById('jitsi-container');

                boton.addEventListener('click', function () {
                    if (api !== null) return;

                    boton.disabled = true;
                    mensaje.textContent = 'Conectando con Jitsi...';

                    const opciones = {
                        roomName: 'ConsultorioLegal-Consulta-{{ $solicitud->id ?? $id ?? 1 }}',
                        width: '100%',
                        height: 600,
                        parentNode: contenedor,
                        lang: 'es',
                        userInfo: {
                            displayName: '{{ $solicitud->cliente ?? "Usuario" }}'
                        }
                    };

                    api = new JitsiMeetExternalAPI('meet.jit.si', opciones);

                    api.addListener('videoConferenceJoined', function () {
                        mensaje.textContent = 'Ya estás dentro de la videollamada.';
                    });

                    api.addListener('videoConferenceLeft', function () {
                        mensaje.textContent = 'Saliste de la videollamada.';
                    });

                    api.addListener('readyToClose', function () {
                        api = null;
                        boton.disabled = false;
                        mensaje.textContent = 'Presioná el botón para iniciar la consulta.';
                    });
                });
            </script>

        {{-- Si la consulta está PENDIENTE: muestra los botones para Aceptar o Rechazar --}}
        @elseif (isset($solicitud->estado) && $solicitud->estado === 'pendiente')
            <section class="informacion" style="background-color: #fff9e6; border: 1px solid #ffe399;">
                <h3 style="color: #8a6d3b; margin-bottom: 10px;">Gestión de Solicitud</h3>
                <p style="color: #8a6d3b; margin-bottom: 15px;">
                    Esta consulta se encuentra pendiente de aprobación. Seleccione una acción:
                </p>

                <div>
                    <button type="button" class="btn-accion btn-aceptar" onclick="alert('Consulta Aceptada')">
                        Aceptar consulta
                    </button>
                    <button type="button" class="btn-accion btn-rechazar" onclick="alert('Consulta Rechazada')">
                        Rechazar consulta
                    </button>
                </div>
            </section>
        @else
            <!-- Mensaje para estado=rechazada -->
            <section class="informacion" style="background-color: #fce8e6; border: 1px solid #fad2cf;">
                <p style="color: #c5221f; font-weight: bold; margin: 0;">
                    La solicitud ha sido rechazada.
                </p>
            </section>
        @endif
    </main>

    <footer>
        Estudio Jurídico · Atención con cita previa
    </footer>

</body>
</html>