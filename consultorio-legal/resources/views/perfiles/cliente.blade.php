
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil</title>
</head>
<body>

    <header>
        <h1>Mi perfil</h1>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </header>

    <section>
        <h2>Mis datos</h2>
        <p>Nombre: {{ auth()->user()->name }}</p>
        <p>Correo: {{ auth()->user()->email }}</p>
    </section>

    <section>
        <h2>Mis consultas</h2>

        @forelse ($solicitudes as $solicitud)
            <article>
                <h3>{{ $solicitud->area }}</h3>
                <p>{{ $solicitud->descripcion }}</p>
                <p>Estado: {{ $solicitud->estado }}</p>

                @if ($solicitud->estado === 'aceptada')
                    <a href="/consultas/{{ $solicitud->id }}">
                        Acceder a mi consulta
                    </a>
                @endif
            </article>
        @empty
            <p>Todavía no tenés consultas registradas.</p>
        @endforelse
    </section>

</body>
</html>
