<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Consulta | Abogado</title>
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
        .btn-accion { padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px; margin-right: 10px; color: white; }
        .btn-aceptar { background-color: #28a745; }
        .btn-rechazar { background-color: #dc3545; }
        #jitsi-container { width: 100%; min-height: 600px; background-color: #000; border-radius: 6px; overflow: hidden; margin-top: 15px; }
        footer { text-align: center; padding: 20px; font-size: 13px; color: #777777; margin-top: auto; }
    </style>
</head>
<body>
    <header>
        <h1>Panel Profesional Abogado</h1>
        <a href="{{ route('abogado') }}" class="btn-volver">
            ← Volver a mi perfil
        </a>
    </header>

    <main>
        <div class="card">
            <h2>Expediente de Consulta #{{ $solicitud->id }}</h2>
            <p style="margin-bottom: 8px;"><strong>Cliente:</strong> {{ $solicitud->nombre }}</p>
            <p style="margin-bottom: 8px;"><strong>Área:</strong> {{ $solicitud->area }}</p>
            <p style="margin-bottom: 8px;">
                <strong>Estado:</strong> 
                <span class="estado-tag estado-{{ strtolower($solicitud->estado) }}">{{ $solicitud->estado }}</span>
            </p>
            <p style="margin-top: 15px;"><strong>Motivo del reclamo / consulta:</strong></p>
            <p style="background: #fafafa; padding: 12px; border: 1px solid #eee; border-radius: 4px; margin-top: 5px;">
                {{ $solicitud->descripcion }}
            </p>

        @if ($solicitud->estado === 'pendiente')
            <div style="display: flex; gap: 10px; margin-top: 20px;">

                <form action="/solicitudes/{{ $solicitud->id }}/aceptar" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn-accion btn-aceptar">
                        Aceptar solicitud
                    </button>
                </form>

                <form action="/solicitudes/{{ $solicitud->id }}/rechazar" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn-accion btn-rechazar">
                        Rechazar solicitud
                    </button>
                </form>

            </div>
        @endif
    </div>

        @if ($solicitud->estado === 'aceptada')

            <div class="card">
                <h2>Turnos de la consulta</h2>

                @forelse ($turnos as $turno)

                    @php
                        $habilitadoDesde = $turno->inicio->copy()->subMinutes(10);
                        $habilitadoHasta = $turno->fin;

                        $ahora = now();

                        $turnoFinalizado = $ahora->gte($habilitadoHasta);

                        $puedeUnirse =
                            $turno->estado === 'reservado' &&
                            $turno->modalidad === 'virtual' &&
                            $ahora->gte($habilitadoDesde) &&
                            $ahora->lt($habilitadoHasta);
                    @endphp

                    <div style="border: 1px solid #ddd;
                                border-radius: 6px;
                                padding: 15px;
                                margin-bottom: 15px;">

                        <h3 style="margin-bottom: 10px;">
                            Turno #{{ $turno->id }}
                        </h3>

                        <p>
                            <strong>Fecha:</strong>
                            {{ $turno->inicio->format('d/m/Y') }}
                        </p>

                        <p>
                            <strong>Horario:</strong>
                            {{ $turno->inicio->format('H:i') }}
                            a
                            {{ $turno->fin->format('H:i') }} hs
                        </p>

                        <p>
                            <strong>Modalidad:</strong>
                            {{ ucfirst($turno->modalidad) }}
                        </p>

                        <p>
                            <strong>Motivo:</strong>
                            {{ $turno->motivo ?? 'Sin especificar' }}
                        </p>

                        <div style="margin-top: 15px;">

                            @if ($turno->estado === 'cancelado')

                                <p style="color: #dc3545;">
                                    <strong>Estado: Cancelado</strong>
                                </p>

                            @elseif ($turnoFinalizado)

                                <p style="color: #137333;">
                                    <strong>Estado: Finalizado</strong>
                                </p>

                                <p>El horario de este turno ha finalizado.</p>

                            @elseif ($puedeUnirse)

                                <p style="color: #137333; margin-bottom: 10px;">
                                    La sala de videollamada está habilitada.
                                </p>

                                <a href="{{ route('turnos.videollamada', $turno) }}"
                                style="display: inline-block;
                                        background: #1a3b47;
                                        color: white;
                                        padding: 10px 20px;
                                        border-radius: 4px;
                                        text-decoration: none;">

                                    Iniciar videollamada

                                </a>

                            @elseif ($turno->modalidad === 'presencial')

                                <p style="color: #b06000;">
                                    <strong>Estado: Turno presencial programado</strong>
                                </p>

                            @else

                                <p style="color: #b06000;">
                                    <strong>Estado: Programado</strong>
                                </p>

                                <p>
                                    La videollamada se habilitará el
                                    {{ $habilitadoDesde->format('d/m/Y') }}
                                    a las
                                    {{ $habilitadoDesde->format('H:i') }} hs.
                                </p>

                            @endif

                        </div>
                    </div>

                @empty

                    <p>Todavía no hay turnos asociados a esta consulta.</p>

                @endforelse

            </div>

        @endif

    </main>
    <footer>Estudio Jurídico · Panel Profesional</footer>
</body>
</html>