<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear perfil | Estudio Jurídico</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #263238;
            background-color: #f5f3ef;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: #183746;
            color: white;
            padding: 24px 8%;
        }

        header h1 {
            margin: 0;
        }

        header p {
            margin-bottom: 0;
        }

        main {
            width: 100%;
            max-width: 500px;
            margin: 60px auto;
            padding: 0 20px;
            flex-grow: 1;
        }

        .card {
            background-color: white;
            padding: 35px;
            border-radius: 8px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07);
        }

        .card h2 {
            color: #183746;
            text-align: center;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .descripcion {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            margin-bottom: 22px;
        }

        input:focus {
            outline: none;
            border-color: #28596b;
        }

        input[readonly] {
            background-color: #f1f1f1;
            color: #555;
        }

        .btn-crear {
            width: 100%;
            padding: 14px;
            background-color: #183746;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-crear:hover {
            background-color: #28596b;
        }

        .error {
            background-color: #fce8e6;
            color: #b42318;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .volver {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #183746;
            text-decoration: none;
            font-weight: bold;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #555;
        }
    </style>
</head>

<body>

    <header>
        <h1>Estudio Jurídico</h1>
        <p>Atención legal personalizada</p>
    </header>

    <main>
        <div class="card">

            <h2>Crear mi perfil</h2>

            <p class="descripcion">
                Tu solicitud fue aceptada.
                Completá tus datos para acceder a tu perfil de cliente.
            </p>

            @if ($errors->any())
                <div class="error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('registro.guardar') }}" method="POST">
                @csrf

                <!-- NOMBRE -->
                <label for="name">Nombre completo</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ $solicitud->nombre }}"
                    readonly
                >

                <!-- CORREO -->
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ $solicitud->email }}"
                    readonly
                >

                <!-- CONTRASEÑA -->
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    minlength="8"
                    required
                >

                <!-- CONFIRMAR CONTRASEÑA -->
                <label for="password_confirmation">
                    Confirmar contraseña
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    minlength="8"
                    required
                >

                <button type="submit" class="btn-crear">
                    Crear perfil
                </button>

            </form>

            <a href="/" class="volver">
                ← Volver al inicio
            </a>

        </div>
    </main>

    <footer>
        Estudio Jurídico · Atención con cita previa
    </footer>

</body>
</html>
