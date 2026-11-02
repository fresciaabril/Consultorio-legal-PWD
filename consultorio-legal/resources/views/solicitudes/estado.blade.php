<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes recibidas</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #263238;
            background-color: #f5f3ef;
        }

        header {
            background-color: #183746;
            color: white;
            padding: 24px 8%;
        }

        header h1 { margin: 0; }

        main {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .tarjeta {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        .tarjeta h3 { margin: 0 0 4px; color: #183746; }
        .tarjeta .email { color: #666; margin: 0 0 10px; }
        .tarjeta p { margin: 6px 0; }

        .estado {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .estado.pendiente { background: #fff3cd; color: #856404; }
        .estado.aceptada  { background: #d4edda; color: #155724; }
        .estado.rechazada { background: #f8d7da; color: #721c24; }

        .acciones { margin-top: 14px; display: flex; gap: 10px; }

        .acciones button {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-aceptar  { background-color: #2e7d32; }
        .btn-rechazar { background-color: #c62828; }
        .acciones button:hover { opacity: 0.85; }

        .vacio { text-align: center; color: #666; }

        .volver {
            display: inline-block;
            margin-top: 10px;
            color: #183746;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <header>
        <h1>Solicitudes recibidas</h1>
        <p>Panel de la abogada</p>
    </header>

    <main>
        @forelse($solicitudes as $s)
            <div class="tarjeta">
                <h3>{{ $s->nombre }}</h3>
                <p class="email">{{ $s->email }} @if($s->telefono) · {{ $s->telefono }} @endif</p>

                <p><strong>Área:</strong> {{ ucfirst($s->area) }}</p>
                <p>{{ $s->descripcion }}</p>

                <p><span class="estado {{ $s->estado }}">{{ ucfirst($s->estado) }}</span></p>

                @if($s->estado === 'pendiente')
                    <div class="acciones">
                        <form action="/solicitudes/{{ $s->id }}/aceptar" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-aceptar">Aceptar</button>
                        </form>

                        <form action="/solicitudes/{{ $s->id }}/rechazar" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-rechazar">Rechazar</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="vacio">Todavía no hay solicitudes.</p>
        @endforelse

        <a class="volver" href="/">← Volver al inicio</a>
    </main>

</body>
</html>