<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Consulta | Cliente</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, sans-serif; }
        body { background-color: #f4f3ef; color: #2c3e50; display: flex; flex-direction: column; min-height: 100vh; }
        header { background-color: #1a3b47; color: #ffffff; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 22px; font-weight: 600; }
        .btn-volver {
            display: inline-flex; align-items: center; gap: 8px; background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3); color: #ffffff; padding: 8px 16px; border-radius: 6px;
            text-decoration: none; font-size: 14px; transition: all 0.2s;
        }
        .btn-volver:hover { background-color: rgba(255, 255, 255, 0.2); }
        main { max-width: 900px; width: 100%; margin: 30px auto; padding: 0 20px; flex-grow: 1; }
        .card { background: #ffffff; border-radius: 6px; padding: 25px; margin-bottom: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.03); }
        .card h2 { font-size: 18px; color: #1a3b47; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .estado-tag { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .estado-aceptada { background-color: #e6f4ea; color: #137333; }
        .estado-pendiente { background-color: #fef7e0; color: #b06000; }
        #jitsi-container { width: 100%; min-height: 600px; background-color: #000; border-radius: 6px; overflow: hidden; margin-top: 15px; }
        button#iniciar { padding: 12px 24px; background-color: #1a3b47; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; font-weight: 600; }
        footer { text-align: center; padding: 20px; font-size: 13px; color: #777777; margin-top: auto; }
    </style>
</head>
<body>
    <header>
        <h1>Estudio Jurídico</h1>
        <a href="javascript:window.close()" class="btn-volver">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            Cerrar ventana
        </a>
    </header>

    <main>
        <div class="card">
            <h2>Detalle de Mi Consulta #{{ $solicitud->id }}</h2>
            <p style="margin-bottom: 8px;"><strong>Área:</strong> {{ $solicitud->area }}</p>
            <p style="margin-bottom: 8px;">
                <strong>Estado:</strong> 
                <span class="estado-tag estado-{{ strtolower($solicitud->estado) }}">{{ $solicitud->estado }}</span>
            </p>
            <p style="margin-top: 15px;"><strong>Descripción del caso:</strong></p>
            <p style="background: #fafafa; padding: 12px; border: 1px solid #eee; border-radius: 4px; margin-top: 5px;">
                {{ $solicitud->descripcion }}
            </p>
        </div>

        @if (isset($solicitud->estado) && $solicitud->estado === 'aceptada')
    @php
        // 1. Tomamos la fecha y hora del turno (ajustar el nombre de la variable cuando Agus conecte su controlador)
        // Ejemplo de prueba: '2026-11-13 19:15:00'
        $fechaHoraTurno = \Carbon\Carbon::parse($turno->fecha_hora ?? '2026-11-13 19:15:00');
        
        // 2. Definimos la ventana de tiempo: desde 10 minutos antes hasta 1 hora después del inicio
        $habilitadoDesde = $fechaHoraTurno->copy()->subMinutes(10);
        $habilitadoHasta = $fechaHoraTurno->copy()->addMinutes(60);
        
        // 3. Verificamos si la hora actual está dentro de ese rango
        $puedeUnirse = now()->between($habilitadoDesde, $habilitadoHasta);
    @endphp

    <section class="informacion">
        <h2>Reunión en Vivo</h2>
        <p style="margin-bottom: 10px;">
            <strong>Turno programado:</strong> {{ $fechaHoraTurno->format('d/m/Y \a \l\a\s H:i') }} hs
        </p>

        @if ($puedeUnirse)
            <p id="mensaje">La sala ya está habilitada. Presioná el botón para iniciar la consulta.</p>
            <button id="iniciar">🎥 Iniciar videollamada</button>
            <div id="jitsi-container"></div>

            <!-- Script de Jitsi Meet (solo se carga si ya es hora del turno) -->
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
                            displayName: '{{ auth()->user()->name ?? "Usuario" }}'
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
        @else
            <!-- Botón deshabilitado visualmente y aviso de horario -->
            <button type="button" disabled style="background-color: #95a5a6; cursor: not-allowed;">
                🔒 Videollamada no disponible aún
            </button>
            <p style="color: #8a6d3b; background-color: #fff9e6; border: 1px solid #ffe399; padding: 12px; border-radius: 4px; margin-top: 10px; font-size: 14px;">
                ⏳ El acceso a la videollamada se habilitará el <strong>{{ $fechaHoraTurno->format('d/m/Y') }}</strong> a partir de las <strong>{{ $habilitadoDesde->format('H:i') }} hs</strong> (10 minutos antes del turno).
            </p>
        @endif
    </section>
        @endif
    </main>
    <footer>Estudio Jurídico · Atención al Cliente</footer>
</body>
</html>