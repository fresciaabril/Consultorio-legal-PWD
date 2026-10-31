<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Profesional | Estudio Jurídico</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f4f3ef;
            color: #2c3e50;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #1a3b47;
            color: #ffffff;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 26px;
            font-weight: 600;
        }

        .btn-logout {
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
        }

        main {
            max-width: 900px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
            flex-grow: 1;
        }

        .card {
            background: #ffffff;
            border-radius: 6px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .card h2 {
            font-size: 20px;
            color: #1a3b47;
            margin-bottom: 15px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 10px;
        }

        .solicitud-item {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .solicitud-item h3 {
            color: #1a3b47;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .solicitud-item p {
            color: #555;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }

        .btn-aceptar {
            background-color: #1a3b47;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-rechazar {
            background-color: #c5221f;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-jitsi {
            display: inline-block;
            background-color: #1a3b47;
            color: #ffffff;
            padding: 8px 14px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #777;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header>
        <h1>Panel del Abogado</h1>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
    </header>

    <main>
        <div class="card">
            <h2>Dr. Martín Alejandro Fernández</h2>
            <p><strong>Especialidades:</strong> Derecho civil, laboral y de familia</p>
            <p><strong>Modalidad:</strong> Atención presencial y virtual</p>
        </div>

        <div class="card">
            <h2>Solicitudes recibidas</h2>

            @forelse ($solicitudes as $solicitud)
                <div class="solicitud-item">
                    <h3>{{ $solicitud->nombre ?? 'Cliente' }} ({{ $solicitud->area }})</h3>
                    <p><strong>Email:</strong> {{ $solicitud->email ?? 'N/A' }}</p>
                    <p>{{ $solicitud->descripcion }}</p>
                    <p><strong>Estado:</strong> {{ $solicitud->estado }}</p>

                    @if ($solicitud->estado === 'pendiente')
                        <form action="/solicitudes/{{ $solicitud->id }}/estado" method="POST" class="actions">
                            @csrf
                            <button type="submit" name="estado" value="aceptada" class="btn-aceptar">
                                Aceptar
                            </button>
                            <button type="submit" name="estado" value="rechazada" class="btn-rechazar">
                                Rechazar
                            </button>
                        </form>
                    @elseif ($solicitud->estado === 'aceptada')
                        <div style="margin-top: 10px;">
                            <a href="https://meet.jit.si/ConsultorioLegal-Consulta-{{ $solicitud->id }}" 
                               target="_blank" 
                               class="btn-jitsi">
                                🎥 Unirse a la reunion
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <p style="color: #777;">No hay solicitudes recibidas.</p>
            @endforelse
        </div>
    </main>

    <footer>
        Estudio Jurídico · Atención con cita previa
    </footer>

</body>
</html>