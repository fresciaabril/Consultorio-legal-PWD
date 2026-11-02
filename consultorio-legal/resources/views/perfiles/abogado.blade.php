<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Abogado | Estudio Jurídico</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .solicitud-info h3 {
            color: #1a3b47;
            font-size: 18px;
            margin-bottom: 6px;
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

        .btn-detalles {
            background-color: #1a3b47;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn-detalles:hover {
            background-color: #122a33;
        }

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
        <h1>Perfil Abogado</h1>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
    </header>

    <main>
        <div class="card">
            <h2>Mis datos profesionales</h2>
            <p style="margin-bottom: 8px;"><strong>Nombre:</strong> Dr. {{ auth()->user()->name ?? 'Abogado de Prueba' }}</p>
            <p><strong>Especialidad:</strong> Derecho Civil y Laboral</p>
        </div>

        <div class="card">
            <h2>Mis consultas</h2>

            @forelse ($solicitudes as $solicitud)
                <div class="solicitud-item">
                    <div class="solicitud-info">
                        <h3>{{ $solicitud->area }}</h3>
                        <span class="estado-tag estado-{{ strtolower($solicitud->estado) }}">
                            {{ $solicitud->estado }}
                        </span>
                    </div>

                    <a href="/consultas/abogado/{{ $solicitud->id }}" target="_blank" class="btn-detalles">
                     Ver consulta
                    </a>
                </div>
            @empty
                <p style="color: #777;">No hay consultas asignadas por el momento.</p>
            @endforelse
        </div>
    </main>

    <footer>
        Estudio Jurídico · Panel Profesional
    </footer>

</body>
</html>