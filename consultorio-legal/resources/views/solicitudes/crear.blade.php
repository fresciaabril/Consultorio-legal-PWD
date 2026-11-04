<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar consulta | Estudio Jurídico</title>

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
            max-width: 650px;
            margin: 50px auto;
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
            text-align: center;
            color: #183746;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .descripcion {
            text-align: center;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-family: inherit;
            font-size: 15px;
            background-color: white;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #28596b;
            box-shadow: 0 0 0 2px rgba(40, 89, 107, 0.12);
        }

        textarea {
            resize: vertical;
        }

        .btn-enviar {
            display: block;
            width: 100%;
            margin-top: 28px;
            padding: 14px;
            border: none;
            border-radius: 6px;
            background-color: #183746;
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-enviar:hover {
            background-color: #28596b;
        }

        .volver {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #183746;
            text-decoration: none;
            font-weight: bold;
        }

        .volver:hover {
            text-decoration: underline;
        }

        .exito {
            background-color: #e6f4ea;
            color: #137333;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .errores {
            background-color: #fce8e6;
            color: #b42318;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .errores ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #555;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .card {
                padding: 25px 20px;
            }
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

            <h2>Solicitar asesoramiento</h2>

            <p class="descripcion">
                Completá el siguiente formulario para que el
                Dr. Martín Alejandro Fernández pueda evaluar tu consulta.
            </p>

            <!-- MENSAJE DE SOLICITUD ENVIADA -->
            @if(session('exito'))
                <div class="exito">
                    {{ session('exito') }}
                </div>
            @endif

            <!-- ERRORES DE VALIDACIÓN -->
            @if($errors->any())
                <div class="errores">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORMULARIO DE SOLICITUD -->
            <form action="/solicitudes" method="POST">

                @csrf

                <!-- NOMBRE -->
                <label for="nombre">Nombre completo</label>

                <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Ingresá tu nombre completo" required>

                <!-- EMAIL -->
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Ingresá tu correo electrónico" required>

                <!-- TELÉFONO -->
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Ingresá tu teléfono">

                <!-- ÁREA -->
                <label for="area">Área de consulta</label>
                <select id="area" name="area" required>
                    <option value="">Seleccioná un área</option>
                    <option value="familia" @selected(old('area') === 'familia')>Derecho de familia</option>
                    <option value="laboral" @selected(old('area') === 'laboral')>Derecho laboral</option>
                    <option value="civil" @selected(old('area') === 'civil')>Derecho civil</option>
                    <option value="otro" @selected(old('area') === 'otro')>Otro</option>
                </select>

                <!-- DESCRIPCIÓN -->
                <label for="descripcion">Contanos el motivo de tu consulta</label>
                <textarea id="descripcion" name="descripcion" rows="5" placeholder="Describí brevemente tu situación..." required>{{ old('descripcion') }}</textarea>

                <!-- ENVIAR -->
                <button type="submit" class="btn-enviar">
                    Enviar solicitud
                </button>

            </form>

            <a href="/" class="volver">← Volver al inicio</a>

        </div>
    </main>

    <footer>
        Estudio Jurídico · Atención con cita previa
    </footer>

</body>
</html>
