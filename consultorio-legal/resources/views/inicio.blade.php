<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudio Jurídico | Inicio</title>

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

        main {
            max-width: 900px;
            width: 100%;
            margin: 70px auto;
            padding: 35px;
            text-align: center;
            flex-grow: 1;
        }

        main h2 {
            color: #183746;
            font-size: 28px;
            margin-bottom: 25px;
        }

        main p {
            line-height: 1.7;
            font-size: 16px;
        }

        .botones {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .botones a {
            padding: 14px 22px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            background-color: #183746;
            color: white;
            transition: background-color 0.2s;
        }

        .botones a:hover {
            background-color: #28596b;
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

        <h2>Bienvenido/a</h2>

        <p>
            Soy el Dr. Martín Alejandro Fernández, abogado.
            Mi objetivo es brindarte asesoramiento jurídico
            personalizado y acompañarte durante tu caso.
        </p>

        <p>
            Desde esta plataforma vas a poder gestionar tus consultas,
            consultar el estado de tus solicitudes y acceder a reuniones
            virtuales cuando corresponda.
        </p>

        <div class="botones">

            <!-- CLIENTES QUE YA TIENEN PERFIL -->
            <a href="{{ route('login') }}">Soy cliente</a>

            <!-- PERSONAS QUE QUIEREN SOLICITAR UNA CONSULTA -->
            <a href="/solicitudes/crear">Solicitar consulta</a>

        </div>

    </main>

    <footer>
        <p>Estudio Jurídico · Atención con cita previa</p>
    </footer>

</body>
</html>
