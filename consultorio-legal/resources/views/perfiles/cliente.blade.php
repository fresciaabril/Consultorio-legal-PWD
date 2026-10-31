<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | Estudio Jurídico</title>
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

        /* Encabezado / Barra superior */
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
            letter-spacing: -0.5px;
        }

        .btn-logout {
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Contenedor principal */
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

        .info-group {
            margin-bottom: 8px;
            font-size: 15px;
        }

        .info-group strong {
            color: #1a3b47;
        }

        /* Tarjeta de Solicitud */
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
            margin-bottom: 8px;
        }

        .solicitud-item p {
            color: #555555;
            font-size: 14px;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .estado-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .estado-aceptada { background-color: #e6f4ea; color: #137333; }
        .estado-pendiente { background-color: #fef7e0; color: #b06000; }
        .estado-rechazada { background-color: #fce8e6; color: #c5221f; }

        /* Botón de Jitsi */
        .btn-jitsi {
            display: inline-block;
            background-color: #1a3b47;
            color: #ffffff;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn-jitsi:hover {
            background-color: #122a33;
        }

        /* Pie de página */
        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #777777;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header>
        <h1>Mi Perfil</h1>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
    </header>

    <main>
        <div class="card">
            <h2>Mis datos personales</h2>
            <div class="info-group">
                <strong>Nombre:</strong> {{ auth()->user()->name ?? 'Cliente de Prueba' }}
            </div>
            <div class="info-group">
                <strong>Correo:</strong> {{ auth()->user()->email ?? 'cliente@prueba.com' }}
            </div>
        </div>

        <div class="card">
            <h2>Mis consultas</h2>

            @forelse ($solicitudes as $solicitud)
                <div class="solicitud-item">
                    <h3>{{ $solicitud->area }}</h3>
                    <p>{{ $solicitud->descripcion }}</p>

                    <div>
                        <span class="estado-tag estado-{{ strtolower($solicitud->estado) }}">
                            Estado: {{ $solicitud->estado }}
                        </span>
                    </div>

                    @if ($solicitud->estado === 'aceptada')
                        <a href="https://meet.jit.si/ConsultorioLegal-Consulta-{{ $solicitud->id }}" 
                           target="_blank" 
                           class="btn-jitsi">
                            🎥 Ingresar a la reunion
                        </a>
                    @endif
                </div>
            @empty
                <p style="color: #777;">Todavía no tenés consultas registradas.</p>
            @endforelse
        </div>
    </main>

    <footer>
        Estudio Jurídico · Atención legal personalizada
    </footer>

</body>
</html>