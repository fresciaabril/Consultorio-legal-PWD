<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudio Jurídico | Asesoramiento Legal</title>
    <style>
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

        /* Encabezado */
        header {
            background-color: #1a3b47;
            color: #ffffff;
            padding: 25px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            font-size: 26px;
            font-weight: 600;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn-nav {
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .btn-nav:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .btn-nav-primary {
            background-color: #ffffff;
            color: #1a3b47;
            border: 1px solid #ffffff;
            padding: 8px 18px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .btn-nav-primary:hover {
            opacity: 0.9;
        }

        /* Contenedor principal en dos columnas: izquierda amplia + derecha vertical */
        main {
            max-width: 1120px;
            width: 100%;
            margin: 35px auto;
            padding: 0 25px;
            flex-grow: 1;
            display: grid;
            grid-template-columns: 1fr 310px;
            gap: 25px;
            align-items: stretch;
        }

        @media (max-width: 768px) {
            header { padding: 18px 15px; flex-direction: column; gap: 12px; text-align: center; }
            main { grid-template-columns: 1fr; padding: 0 15px; margin: 20px auto; }
        }

        .columna-izquierda {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .card {
            background: #ffffff;
            border-radius: 6px;
            padding: 28px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        /* Tarjeta vertical derecha */
        .card-vertical {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .card h3 {
            font-size: 20px;
            color: #1a3b47;
            margin-bottom: 14px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 10px;
        }

        .card p {
            font-size: 15px;
            line-height: 1.6;
            color: #444444;
        }

        /* Lista vertical de especialidades dentro de la tarjeta derecha */
        .lista-areas {
            list-style: none;
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .lista-areas li {
            background-color: #fafafa;
            border-left: 3px solid #1a3b47;
            padding: 12px 14px;
            border-radius: 0 4px 4px 0;
        }

        .lista-areas strong {
            display: block;
            color: #1a3b47;
            font-size: 15px;
            margin-bottom: 4px;
        }

        .lista-areas span {
            font-size: 13.5px;
            color: #555555;
            line-height: 1.45;
            display: block;
        }

        .badge-especialidad {
            display: inline-block;
            background-color: #e6eef2;
            color: #1a3b47;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .cta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .btn-consulta {
            display: inline-block;
            background-color: #1a3b47;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 15px;
            transition: background-color 0.2s;
        }

        .btn-consulta:hover {
            background-color: #122a33;
        }

        .btn-secundario {
            display: inline-block;
            background-color: transparent;
            color: #1a3b47;
            border: 1px solid #1a3b47;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 15px;
            transition: background-color 0.2s;
        }

        .btn-secundario:hover {
            background-color: #f0f4f6;
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

    <header>
        <h1>Estudio Jurídico</h1>
        <div class="nav-actions">
            @auth
                @if (auth()->user()->rol === 'abogado')
                    <a href="/abogado" class="btn-nav-primary">Ir a mi Panel Profesional</a>
                @else
                    <a href="/perfil" class="btn-nav-primary">Mis Consultas y Turnos</a>
                @endif
            @else
                <a href="/login" class="btn-nav">Iniciar sesión</a>
            @endauth
        </div>
    </header>

    <main>
        <!-- COLUMNA IZQUIERDA: Sobre el profesional arriba + Asesoramiento abajo -->
        <div class="columna-izquierda">
            <!-- 1. Presentación profesional dinámica desde "Mi Perfil" -->
            <div class="card">
                <h3>Sobre el Profesional</h3>
                <div class="badge-especialidad">
                    {{ session('perfil_abogado.especialidades', 'Derecho Civil, Laboral y de Familia') }}
                </div>
                <p style="font-weight: 600; color: #1a3b47; margin-bottom: 8px;">
                    {{ session('perfil_abogado.nombre', 'Dr. Martín Alejandro Fernández') }}
                </p>
                <p>
                    {{ session('perfil_abogado.descripcion_inicio', 'Brindamos acompañamiento jurídico integral con atención personalizada, análisis detallado de cada caso y seguimiento continuo mediante consultas virtuales programadas.') }}
                </p>
            </div>

            <!-- 2. Asesoramiento y Modalidad de Atención unificados abajo -->
            <div class="card">
                <h3>Asesoramiento y Modalidad de Atención</h3>
                <p style="margin-bottom: 14px;">
                    Ofrecemos asesoramiento legal claro y personalizado. Enviá tu consulta online, coordiná un turno según disponibilidad y realizá tu reunión virtual directamente desde nuestra plataforma:
                </p>
                <p>
                    1. Enviá tu consulta detallando tu caso.<br>
                    2. Una vez aceptada, verificá tu correo y elegí un turno disponible.<br>
                    3. Conectate a tu videollamada el día y horario acordados.
                </p>

                <div class="cta-actions">
                    @auth
                        @if (auth()->user()->rol === 'abogado')
                            <a href="/abogado" class="btn-consulta">Ir al Panel del Abogado</a>
                        @else
                            <a href="/solicitudes/crear" class="btn-consulta">Hacer una consulta</a>
                            <a href="/perfil" class="btn-secundario">Ver mis consultas</a>
                        @endif
                    @else
                        <a href="/solicitudes/crear" class="btn-consulta">Hacer una consulta</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Tarjeta vertical de Áreas de Práctica -->
        <aside class="card card-vertical">
            <div>
                <h3>Áreas de Práctica</h3>
                <p>
                    Asesoramiento y representación integral en las siguientes ramas del derecho:
                </p>

                <ul class="lista-areas">
                    <li>
                        <strong>Derecho Civil</strong>
                        <span>Contratos de alquiler, compraventa, sucesiones, daños y perjuicios y reclamos particulares.</span>
                    </li>
                    <li>
                        <strong>Derecho Laboral</strong>
                        <span>Despidos sin causa, liquidaciones finales, accidentes de trabajo y diferencias salariales.</span>
                    </li>
                    <li>
                        <strong>Derecho de Familia</strong>
                        <span>Divorcios, cuota alimentaria, régimen de comunicación y acuerdos familiares.</span>
                    </li>
                </ul>
            </div>
        </aside>
    </main>

    <footer>
        Estudio Jurídico · Atención con cita previa
    </footer>

</body>
</html>