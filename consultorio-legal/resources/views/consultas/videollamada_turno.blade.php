<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videollamada Turno #{{ $turno->id }} | Estudio Jurídico</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #f4f3ef; color: #2c3e50; display: flex; flex-direction: column; min-height: 100vh; }
        header { background-color: #1a3b47; color: #ffffff; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 22px; font-weight: 600; }
        .btn-volver { background-color: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; }
        main { max-width: 960px; width: 100%; margin: 30px auto; padding: 0 20px; flex-grow: 1; }
        .card { background: #ffffff; border-radius: 6px; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.03); }
        .card h2 { font-size: 18px; color: #1a3b47; margin-bottom: 12px; border-bottom: 1px solid #eee; padding-bottom: 8px; }
        .btn-iniciar { background-color: #137333; color: #fff; border: none; padding: 12px 22px; border-radius: 4px; font-weight: 600; font-size: 15px; cursor: pointer; }
        #jitsi-container { width: 100%; min-height: 560px; background-color: #111; border-radius: 6px; overflow: hidden; margin-top: 15px; }
        footer { text-align: center; padding: 20px; font-size: 13px; color: #777; margin-top: auto; }
        @media (max-width: 768px) {
            header { padding: 18px 15px; flex-direction: column; gap: 12px; text-align: center; }
            main { padding: 0 12px; margin: 18px auto; }
            #jitsi-container { height: 420px; min-height: 420px; }
        }
    </style>
</head>
<body>
    <header>
        <h1>Sala Virtual · Turno #{{ $turno->id }}</h1>
        <a href="{{ auth()->user()->rol === 'abogado' ? route('abogado') : route('perfil') }}" class="btn-volver">
            Volver a mi panel
        </a>
    </header>

    <main>
        @php
            $inicioTurno = \Carbon\Carbon::parse($turno->inicio);
            $finTurno = \Carbon\Carbon::parse($turno->fin);
            $habilitadoDesde = $inicioTurno->copy()->subMinutes(10);
            $habilitadoHasta = $finTurno->copy();

            $ahora = now();

            $puedeUnirse =
                $turno->estado === 'reservado' &&
                $turno->modalidad === 'virtual' &&
                $ahora->gte($habilitadoDesde) &&
                $ahora->lt($habilitadoHasta);
        @endphp

        <div class="card">
            <h2>Detalle del turno virtual</h2>
            <p style="margin-bottom: 6px;"><strong>Profesional:</strong> {{ $turno->abogado->name ?? 'Abogado' }}</p>
            <p style="margin-bottom: 6px;"><strong>Cliente:</strong> {{ $turno->cliente->name ?? 'Cliente' }}</p>
            <p style="margin-bottom: 6px;"><strong>Fecha y horario:</strong> {{ $inicioTurno->format('d/m/Y') }} de {{ $inicioTurno->format('H:i') }} a {{ $finTurno->format('H:i') }} hs</p>
            @if ($turno->motivo)
                <p><strong>Motivo:</strong> {{ $turno->motivo }}</p>
            @endif
        </div>

        <div class="card">
            <h2>Videollamada</h2>
            @if ($puedeUnirse)
                <p id="mensaje" style="margin-bottom: 14px;">La sala está habilitada. Presioná el botón para conectarte.</p>
                <button type="button" id="btn-iniciar-jitsi" class="btn-iniciar">Iniciar videollamada</button>
                <div id="jitsi-container"></div>

                <script src="https://meet.jit.si/external_api.js"></script>
                <script>
                    let api = null;
                    document.getElementById('btn-iniciar-jitsi').addEventListener('click', function() {
                        if (api !== null) return;
                        this.disabled = true;
                        document.getElementById('mensaje').textContent = 'Conectando a la sala privada...';

                        api = new JitsiMeetExternalAPI('meet.jit.si', {
                            roomName: 'ConsultorioLegal-Turno-{{ $turno->id }}',
                            width: '100%',
                            height: 560,
                            parentNode: document.getElementById('jitsi-container'),
                            lang: 'es',
                            userInfo: {
                                displayName: @json(auth()->user()->name)
                            }
                        });
                    });
                </script>
            @else
                <button type="button" disabled style="background-color: #95a5a6; color: #fff; border: none; padding: 12px 22px; border-radius: 4px; font-weight: 600; cursor: not-allowed;">
                    Videollamada no disponible aún
                </button>
                <p style="color: #8a6d3b; background-color: #fff9e6; border: 1px solid #ffe399; padding: 12px; border-radius: 4px; margin-top: 12px; font-size: 14px;">
                    El acceso está habilitado únicamente para turnos virtuales reservados el
                    <strong>{{ $inicioTurno->format('d/m/Y') }}</strong> desde las <strong>{{ $habilitadoDesde->format('H:i') }} hs</strong> (10 minutos antes del inicio) hasta las <strong>{{ $habilitadoHasta->format('H:i') }} hs</strong> (finalización del turno).
                </p>
            @endif
        </div>
    </main>

    <footer>Estudio Jurídico · Sala de Consulta Virtual</footer>
</body>
</html>