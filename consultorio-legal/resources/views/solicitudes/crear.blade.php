
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva consulta</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #263238;
            background-color: #f5f3ef;
        }

        main {
            max-width: 600px;
            margin: 50px auto;
            padding: 35px;
            background: white;
            border-radius: 8px;
        }

        h1 { text-align: center; color: #183746; }

        label {
            display: block;
            margin-top: 16px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-family: inherit;
        }

        button {
            margin-top: 24px;
            padding: 14px 22px;
            border: none;
            border-radius: 6px;
            background-color: #183746;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover { background-color: #28596b; }

        a { color: #183746; display: inline-block; margin-top: 20px; }
    </style>

</head>
<body>

    <h1>Solicitar asesoramiento</h1>
    <p>Completá el formulario para que la abogada pueda evaluar tu consulta.</p>

    @if(session('exito'))
        <p style="color: green;">{{ session('exito') }}</p>
    @endif

    @if($errors->any())
        <ul style="color: red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="/solicitudes" method="POST">
        @csrf

        <label for="nombre">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required><br>

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required><br>

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}"><br>

        <label for="area">Área de consulta</label>
        <select id="area" name="area" required>
            <option value="">Seleccioná un área</option>
            <option value="familia" @selected(old('area') === 'familia')>Derecho de familia</option>
            <option value="laboral" @selected(old('area') === 'laboral')>Derecho laboral</option>
            <option value="civil" @selected(old('area') === 'civil')>Derecho civil</option>
            <option value="otro" @selected(old('area') === 'otro')>Otro</option>
        </select><br>

        <label for="descripcion">Contanos el motivo de tu consulta</label><br>
        <textarea id="descripcion" name="descripcion"
                rows="5" required>{{ old('descripcion') }}</textarea><br>

        <button type="submit">Enviar solicitud</button>
    </form>

    <a href="/">Volver al inicio</a>

</body>
</html>
