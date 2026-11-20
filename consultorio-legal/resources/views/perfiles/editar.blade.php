<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil Profesional | Estudio Jurídico</title>
    <style>
        /* Reseteo básico y tipografía consistente con el resto del proyecto */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f4f3ef;
            color: #2c3e50;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        /* Barra superior en azul petróleo */
        header {
            background-color: #1a3b47;
            color: #ffffff;
            padding: 22px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        /* Botones de navegación en el encabezado */
        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

    

        /* Estilo unificado para Volver al Panel y Cerrar sesión */
        .btn-logout {
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            line-height: normal;
            transition: background-color 0.2s;
        }

        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Contenedor central de la tarjeta */
        main {
            max-width: 820px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
            flex-grow: 1;
        }

        .card {
            background: #ffffff;
            border-radius: 6px;
            padding: 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .card h2 {
            font-size: 20px;
            color: #1a3b47;
            margin-bottom: 8px;
        }

        .subtitulo {
            font-size: 14px;
            color: #666666;
            margin-bottom: 22px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 14px;
        }

        /* Distribución en grilla de dos columnas para campos cortos */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 18px;
            margin-bottom: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 18px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #1a3b47;
        }

        .form-group small {
            font-size: 12px;
            color: #777777;
        }

        .form-group input,
        .form-group textarea {
            padding: 11px 12px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fafafa;
            color: #2c3e50;
            line-height: 1.5;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1a3b47;
            background-color: #ffffff;
        }

        /* Alerta visual cuando se guardan los datos correctamente */
        .alerta-exito {
            background-color: #e6f4ea;
            color: #137333;
            border: 1px solid #b7e1cd;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Alerta visual si falla la validación de algún campo */
        .alerta-error {
            background-color: #fce8e6;
            color: #c5221f;
            border: 1px solid #f5c2c7;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Botonera inferior del formulario */
        .acciones {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-guardar {
            background-color: #1a3b47;
            color: #ffffff;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-guardar:hover {
            background-color: #122a33;
        }

        .btn-cancelar {
            background-color: #e6e4dd;
            color: #1a3b47;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 15px;
        }

        .btn-cancelar:hover {
            background-color: #d8d5cc;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #777777;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <!-- Encabezado con enlace para regresar al panel principal del abogado -->
    <header>
        <h1>Mi Perfil</h1>
        <div class="header-actions">
            <a href="{{ route('abogado') }}" class="btn-logout">Volver al Panel</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main>
        <!-- Muestra el mensaje de confirmación si la ruta PUT redirigió con session('exito') -->
        @if (session('exito'))
            <div class="alerta-exito">
                {{ session('exito') }}
            </div>
        @endif

        <!-- Muestra errores de validación de Laravel si algún campo queda vacío o inválido -->
        @if ($errors->any())
            <div class="alerta-error">
                <ul style="margin-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <h2>Mis datos</h2>
            <p class="subtitulo">
                Actualizá tus datos personales y la descripción que se muestra en la página de inicio del estudio.
            </p>
            <form action="{{ route('abogado.perfil.actualizar') }}" method="POST">
                @csrf
                <!-- Directiva Blade que simula el método HTTP PUT para actualizar registros -->
                @method('PUT')

                <div class="form-grid">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="nombre">Nombre profesional</label>
                        <!-- old('nombre', ...) conserva lo que escribió el usuario si hubo error de validación -->
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $perfil['nombre']) }}" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="email">Correo electrónico</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $perfil['email']) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="especialidades">Especialidad</label>
                    <input type="text" name="especialidades" id="especialidades" value="{{ old('especialidades', $perfil['especialidades']) }}" required>
                </div>

                <div class="form-group">
                    <label for="descripcion_inicio">Descripción para la página de inicio</label>
                    <small>Este texto aparecerá en la portada principal para que los clientes conozcan tu trayectoria y áreas de atención.</small>
                    <textarea name="descripcion_inicio" id="descripcion_inicio" rows="5" required>{{ old('descripcion_inicio', $perfil['descripcion_inicio']) }}</textarea>
                </div>

                <div class="acciones">
                    <a href="{{ route('abogado') }}" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">Guardar cambios</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        Estudio Jurídico · Gestión de Perfil Profesional
    </footer>

</body>
</html>