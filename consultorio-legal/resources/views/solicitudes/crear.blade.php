
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva consulta</title>
</head>
<body>

    <h1>Solicitar asesoramiento</h1>
    <p>Completá el formulario para que la abogada pueda evaluar tu consulta.</p>

    <form action="/solicitudes" method="POST">
        @csrf

        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required>

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono">

        <label for="area">Área de consulta</label>
        <select id="area" name="area" required>
            <option value="">Seleccioná un área</option>
            <option value="familia">Derecho de familia</option>
            <option value="laboral">Derecho laboral</option>
            <option value="civil">Derecho civil</option>
            <option value="otro">Otro</option>
        </select>

        <label for="descripcion">Contanos el motivo de tu consulta</label>
        <textarea id="descripcion" name="descripcion"
                rows="5" required></textarea>

        <button type="submit">Enviar solicitud</button>
    </form>

    <a href="/">Volver al inicio</a>

</body>
</html>
