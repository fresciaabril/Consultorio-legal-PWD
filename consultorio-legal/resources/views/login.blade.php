
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
</head>
<body>

    <h1>Ingresar a mi cuenta</h1>
    <p>Accedé a tu perfil de cliente.</p>

    <form action="/login" method="POST">
        @csrf

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        @if ($errors->any())
            <p>{{ $errors->first() }}</p>
        @endif

        <button type="submit">Ingresar</button>
    </form>

    <p>
        ¿Todavía no tenés una cuenta?
        <a href="/registro">Registrarme</a>
    </p>

    <a href="/">Volver al inicio</a>

</body>
</html>
