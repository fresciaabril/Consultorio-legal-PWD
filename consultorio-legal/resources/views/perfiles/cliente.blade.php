<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | Estudio Jurídico</title>

    <!-- Librería FullCalendar v6 (CDN oficial) -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>

    <style>
        /* Reseteo básico y tipografía coherente con el estudio */
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

        .btn-logout {
            background-color: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Contenedor principal en 2 columnas: sidebar izquierda + pestañas y contenido a la derecha */
        main {
            max-width: 1200px;
            width: 100%;
            margin: 35px auto;
            padding: 0 25px;
            flex-grow: 1;
            display: flex;
            align-items: flex-start;
            gap: 25px;
        }

        /* Columna izquierda: tarjeta vertical de datos personales */
        .sidebar-perfil {
            width: 280px;
            flex-shrink: 0;
            position: sticky;
            top: 25px;
        }

        .sidebar-perfil .card {
            margin-bottom: 0;
        }

        .dato-vertical {
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .dato-vertical:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .dato-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6c7a86;
            margin-bottom: 4px;
            letter-spacing: 0.4px;
        }

        .dato-valor {
            font-size: 15px;
            font-weight: 500;
            color: #1a3b47;
            word-break: break-word;
        }

        /* Columna derecha: botones de pestañas arriba + paneles abajo */
        .contenido-principal {
            flex: 1;
            min-width: 0;
        }

        .card {
            background: #ffffff;
            border-radius: 6px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        }

        .card h2 {
            font-size: 20px;
            color: #1a3b47;
            margin-bottom: 15px;
            border-bottom: 1px solid #eeeeee;
            padding-bottom: 10px;
        }

        /* Barra de pestañas horizontales alineadas a la derecha de la tarjeta personal */
        .tabs-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #dcdad3;
            padding-bottom: 10px;
        }

        .tab-btn {
            background-color: #e6e4dd;
            color: #1a3b47;
            border: none;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            flex: 1;
            text-align: center;
            min-width: 140px;
        }

        .tab-btn:hover {
            background-color: #d8d5cc;
        }

        .tab-btn.active {
            background-color: #1a3b47;
            color: #ffffff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Tarjetas internas de consultas y turnos */
        .solicitud-item {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .solicitud-info h3 {
            color: #1a3b47;
            font-size: 18px;
            margin-bottom: 6px;
        }

        .estado-tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .estado-aceptada { background-color: #e6f4ea; color: #137333; }
        .estado-pendiente { background-color: #fef7e0; color: #b06000; }
        .estado-rechazada { background-color: #fce8e6; color: #c5221f; }

        .btn-detalles {
            background-color: #1a3b47;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            display: inline-block;
        }

        .btn-detalles:hover {
            background-color: #122a33;
        }

        .btn-cancelar-turno {
            background-color: transparent;
            color: #c5221f;
            border: 1px solid #c5221f;
            padding: 9px 16px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        .btn-cancelar-turno:hover {
            background-color: #fce8e6;
        }

        /* Panel desplegable de confirmación de reserva al hacer clic en FullCalendar */
        .panel-reserva {
            display: none;
            background-color: #f4f7f8;
            border: 1px solid #cbd8de;
            border-left: 4px solid #1a3b47;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .panel-reserva h3 {
            color: #1a3b47;
            font-size: 17px;
            margin-bottom: 12px;
        }

        .form-reserva-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #1a3b47;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #ffffff;
        }

        /* Personalización de botones de FullCalendar */
        .fc .fc-button-primary {
            background-color: #1a3b47;
            border-color: #1a3b47;
        }

        .fc .fc-button-primary:hover {
            background-color: #122a33;
            border-color: #122a33;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #0d1f26;
            border-color: #0d1f26;
        }

        .fc-toolbar-title {
            color: #1a3b47;
            font-size: 1.2rem !important;
            text-transform: capitalize;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #777777;
            margin-top: auto;
        }

        /* Adaptación automática en pantallas chicas (celulares/tablets) */
        @media (max-width: 820px) {
            main {
                flex-direction: column;
            }
            .sidebar-perfil {
                width: 100%;
                position: static;
            }
            .contenido-principal {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <header>
        <h1>Mi Perfil</h1>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
    </header>

    <main>
        <!-- COLUMNA IZQUIERDA: TARJETA VERTICAL DE DATOS PERSONALES -->
        <aside class="sidebar-perfil">
            <div class="card">
                <h2>Mis datos personales</h2>

                <div class="dato-vertical">
                    <span class="dato-label">Nombre</span>
                    <span class="dato-valor">{{ auth()->user()->name ?? 'Cliente' }}</span>
                </div>

                <div class="dato-vertical">
                    <span class="dato-label">Correo electrónico</span>
                    <span class="dato-valor">{{ auth()->user()->email ?? 'cliente@ejemplo.com' }}</span>
                </div>

                <div class="dato-vertical">
                    <span class="dato-label">Tipo de cuenta</span>
                    <span class="dato-valor">Cliente</span>
                </div>
            </div>
        </aside>

        <!-- COLUMNA DERECHA: BOTONES DE PESTAÑAS + CONTENIDO DE CADA SECCIÓN -->
        <div class="contenido-principal">
            <!-- muestra mensaje de confirmacion del turno -->
            @if (session('success'))
                <div style="background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6; padding: 14px 18px; border-radius: 6px; margin-bottom: 18px; font-size: 14px; font-weight: 500;">
                    {{ session('success') }}
                </div>
            @endif
            <!-- Botonera de 3 pestañas horizontales alineadas al lado de la tarjeta -->
            <div class="tabs-nav">
                <button type="button" class="tab-btn active" onclick="cambiarPestanaCliente(event, 'tab-mis-consultas')">
                    Mis Consultas
                </button>
                <button type="button" class="tab-btn" onclick="cambiarPestanaCliente(event, 'tab-reservar-turno')">
                    Reservar Turno
                </button>
                <button type="button" class="tab-btn" onclick="cambiarPestanaCliente(event, 'tab-mis-turnos')">
                    Mis Turnos Agendados
                </button>
            </div>

            <!-- PESTAÑA 1: MIS CONSULTAS -->
            <div id="tab-mis-consultas" class="tab-content active">
                <div class="card">
                    <h2>Mis consultas</h2>

                    @forelse ($solicitudes as $solicitud)
                        <div class="solicitud-item">
                            <div class="solicitud-info">
                                <h3>{{ $solicitud->area }}</h3>
                                <span class="estado-tag estado-{{ strtolower($solicitud->estado) }}">
                                    {{ $solicitud->estado }}
                                </span>
                            </div>
                            <a href="/consultas/cliente/{{ $solicitud->id }}" target="_blank" class="btn-detalles">
                                Ver detalles
                            </a>
                        </div>
                    @empty
                        <p style="color: #777777;">Todavía no tenés consultas registradas.</p>
                    @endforelse
                </div>
            </div>

            <!-- PESTAÑA 2: RESERVAR TURNO (CON FULLCALENDAR) -->
            <div id="tab-reservar-turno" class="tab-content">
                <div class="card">
                    <h2>Reservar Turno Online</h2>
                    <p style="font-size: 14px; color: #555555; margin-bottom: 18px;">
                        Seleccioná un bloque horario disponible en el calendario para coordinar tu entrevista con el abogado.
                    </p>

                    <!-- Formulario que se despliega al hacer clic sobre un horario libre -->
                    <div id="panel-confirmar-reserva" class="panel-reserva">
                        <h3>Confirmar reserva de turno</h3>
                        <form action="/turnos" method="POST">
                            @csrf
                            <div class="form-reserva-grid">
                                <div class="form-group">
                                    <label for="solicitud_id">Consulta aceptada asociada</label>
                                    <select name="solicitud_id" id="solicitud_id" required>
                                        @foreach ($solicitudes->where('estado', 'aceptada') as $aceptada)
                                            <option value="{{ $aceptada->id }}">
                                                Consulta #{{ $aceptada->id }} - {{ ucfirst($aceptada->area) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="fecha_turno">Fecha seleccionada</label>
                                    <input type="text" name="fecha" id="fecha_turno" readonly required>
                                </div>

                                <div class="form-group">
                                    <label for="hora_turno">Horario</label>
                                    <input type="text" name="hora" id="hora_turno" readonly required>
                                </div>
                            </div>

                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn-detalles">Confirmar Turno</button>
                                <button type="button" class="btn-cancelar-turno" onclick="cerrarPanelReserva()">Cancelar selección</button>
                            </div>
                        </form>
                    </div>

                    <!-- Contenedor donde se dibuja FullCalendar -->
                    <div id="calendario-cliente"></div>
                </div>
            </div>

            <!-- PESTAÑA 3: MIS TURNOS AGENDADOS -->
            <div id="tab-mis-turnos" class="tab-content">
                <div class="card">
                    <h2>Mis turnos agendados</h2>

                    @forelse ($turnos ?? [] as $turno)
                        <div class="solicitud-item">
                            <div class="solicitud-info">
                                <h3>Turno para Consulta #{{ $turno->solicitud_id }}</h3>
                                <p style="font-size: 14px; color: #555555; margin-bottom: 6px;">
                                    Fecha: <strong>{{ $turno->fecha }}</strong> · Hora: <strong>{{ $turno->hora }}</strong>
                                </p>
                                <span class="estado-tag estado-aceptada">Confirmado</span>
                            </div>

                            <div style="display: flex; gap: 10px; align-items: center;">
                                <a href="/turnos/{{ $turno->id }}/videollamada" class="btn-detalles">
                                    Sala de reunión
                                </a>
                                <form action="/turnos/{{ $turno->id }}/cancelar" method="POST" style="margin: 0;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-cancelar-turno">Cancelar turno</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p style="color: #777777;">Todavía no tenés turnos agendados. Podés reservar uno desde la pestaña "Reservar Turno".</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <footer>
        Estudio Jurídico · Atención con cita previa
    </footer>

    <script>
        let calendarioCliente = null;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendario-cliente');
            if (!calendarEl) return;

            calendarioCliente = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                locale: 'es',
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                allDaySlot: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana'
                },
                // Bloques horarios de prueba mientras Male integra GET /agenda/disponibilidad
                // Luego reemplazar por: events: '/agenda/disponibilidad'
                events: [
                    {
                        title: 'Disponible',
                        start: '2026-11-16T10:00:00',
                        end: '2026-11-16T11:00:00',
                        color: '#137333'
                    },
                    {
                        title: 'Disponible',
                        start: '2026-11-16T11:00:00',
                        end: '2026-11-16T12:00:00',
                        color: '#137333'
                    },
                    {
                        title: 'Disponible',
                        start: '2026-11-18T15:00:00',
                        end: '2026-11-18T16:00:00',
                        color: '#137333'
                    }
                ],
                eventClick: function(info) {
                    const inicio = info.event.start;
                    const fechaStr = inicio.toISOString().split('T')[0];
                    const horaStr = inicio.toTimeString().slice(0, 5);

                    document.getElementById('fecha_turno').value = fechaStr;
                    document.getElementById('hora_turno').value = horaStr;
                    document.getElementById('panel-confirmar-reserva').style.display = 'block';
                }
            });

            calendarioCliente.render();
        });

        function cambiarPestanaCliente(evento, idPestana) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

            document.getElementById(idPestana).classList.add('active');
            evento.currentTarget.classList.add('active');

            if (idPestana === 'tab-reservar-turno' && calendarioCliente) {
                setTimeout(() => {
                    calendarioCliente.updateSize();
                }, 50);
            }
        }

        function cerrarPanelReserva() {
            document.getElementById('panel-confirmar-reserva').style.display = 'none';
        }
    </script>

</body>
</html>