
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel profesional</title>
</head>
<body>

    <header>
        <h1>Panel del abogado</h1>

        <form action="/logout" method="POST">
            @csrf
            <button type="submit">Cerrar sesión</button>
        </form>
    </header>

    <section>
        <h1>Dr. Martín Alejandro Fernández</h1>
        <p>Abogado</p>
        <p>Estudio Jurídico Fernández</p>
        <p>Especialidades: Derecho civil, laboral y de familia</p>
        <p>Atención presencial y virtual</p>
    </section>

    <section>
        <h2>Solicitudes recibidas</h2>

        @forelse ($solicitudes as $solicitud)
            <article>
                <h3>{{ $solicitud->nombre }}</h3>
                <p>Correo: {{ $solicitud->email }}</p>
                <p>Área: {{ $solicitud->area }}</p>
                <p>{{ $solicitud->descripcion }}</p>
                <p>Estado: {{ $solicitud->estado }}</p>

                @if ($solicitud->estado === 'pendiente')
                    <form action="/solicitudes/{{ $solicitud->id }}/estado"
                        method="POST">
                        @csrf

                        <button type="submit" name="estado"
                                value="aceptada">
                            Aceptar
                        </button>

                        <button type="submit" name="estado"
                                value="rechazada">
                            Rechazar
                        </button>
                    </form>
                @endif
            </article>
        @empty
            <p>No hay solicitudes recibidas.</p>
        @endforelse
    </section>

</body>
</html>
