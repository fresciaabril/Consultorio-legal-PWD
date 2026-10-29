
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Consulta virtual | Estudio Jurídico</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f4;
            color: #222;
        }

        header {
            background-color: #172b4d;
            color: #D4A964;
            padding: 20px;
            text-align: center;
            font-family: playfair display;
        }

        main {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
        }

        .informacion {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        #jitsi-container {
            width: 100%;
            min-height: 600px;
            background-color: #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        #mensaje {
            margin-bottom: 15px;
        }

        button {
            padding: 12px 20px;
            background-color: #172b4d ;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body>

    <header>
        <h1>Estudio Jurídico</h1>
        <p>Consultas virtuales</p>
    </header>

    <main>
        <section class="informacion">
            <h2>Consulta de prueba</h2>

            <p><strong>Cliente:</strong> Usuario de prueba</p>
            <p><strong>Abogado:</strong> Abogado de prueba</p>
            <p><strong>Modalidad:</strong> Virtual</p>

            <p>
                Esta es una demostración de videollamada
                integrada en nuestra página web.
            </p>
        </section>

        <h2>Videollamada</h2>

        <p id="mensaje">
            Presioná el botón para iniciar la consulta.
        </p>

        <button id="iniciar">
            Iniciar videollamada
        </button>

        <div id="jitsi-container"></div>
    </main>

    <script src="https://meet.jit.si/external_api.js"></script>

    <script>
        let api = null;

        const boton = document.getElementById('iniciar');
        const mensaje = document.getElementById('mensaje');
        const contenedor = document.getElementById('jitsi-container');

        boton.addEventListener('click', function () {

            if (api !== null) {
                return;
            }

            boton.disabled = true;
            mensaje.textContent = 'Conectando con Jitsi...';

            const opciones = {
                roomName: 'EstudioJuridicoDemo2026Prueba',
                width: '100%',
                height: 600,
                parentNode: contenedor,
                lang: 'es',
                userInfo: {
                    displayName: 'Usuario de prueba'
                }
            };

            api = new JitsiMeetExternalAPI(
                'meet.jit.si',
                opciones
            );

            api.addListener('videoConferenceJoined', function () {
                mensaje.textContent =
                    'Ya estás dentro de la videollamada.';
            });

            api.addListener('videoConferenceLeft', function () {
                mensaje.textContent =
                    'Saliste de la videollamada.';
            });

            api.addListener('readyToClose', function () {
                api = null;
                boton.disabled = false;
            });
        });
    </script>

</body>
</html>
