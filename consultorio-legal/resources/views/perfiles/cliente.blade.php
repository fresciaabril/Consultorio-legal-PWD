<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        /* Alertas dinámicas (éxito / error) */
        .alerta {
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: 500;
            display: none;
        }

        .alerta-exito {
            background-color: #e6f4ea;
            color: #137333;
            border: 1px solid #ceead6;
        }

        .alerta-error {
            background-color: #fce8e6;
            color: #c5221f;
            border: 1px solid #f6aea9;
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
            flex-wrap: wrap;
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

        .estado-aceptada, .estado-reservado { background-color: #e6f4ea; color: #137333; }
        .estado-pendiente { background-color: #fef7e0; color: #b06000; }
        .estado-rechazada, .estado-cancelado { background-color: #fce8e6; color: #c5221f; }

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

        .btn-detalles:disabled {
            background-color: #7f949c;
            cursor: not-allowed;
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
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #ffffff;
        }

        .acciones-form-reserva {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Personalización de botones y bloques de FullCalendar */
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

        .fc-event {
            cursor: pointer;
        }

        .fc-daygrid-event {
            border-radius: 4px !important;
            padding: 3px 6px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            border: none !important;
            background-color: #137333 !important;
            color: #ffffff !important;
            margin-bottom: 3px !important;
            text-align: center;
        }

        .fc-daygrid-event:hover {
            background-color: #0d5524 !important;
        }

        .fc-daygrid-event-dot,
        .fc-event-time {
            display: none !important;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #777777;
            margin-top: auto;
        }

        /* Diseño Responsive para celulares y tablets */
        @media (max-width: 768px) {
            header {
                padding: 18px 15px;
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            header h1 {
                font-size: 22px;
            }
            /* Apila sidebar arriba y contenido abajo */
            main {
                flex-direction: column;
                padding: 0 12px;
                margin: 15px auto;
                gap: 15px;
            }
            .sidebar-perfil {
                position: static;
                width: 100%;
            }
            .contenido-principal {
                width: 100%;
            }
            .tabs-nav {
                flex-direction: column;
                gap: 8px;
            }
            .tab-btn {
                min-width: 0;
                width: 100%;
                padding: 11px 12px;
            }
            .card {
                padding: 16px;
            }
            .card h2 {
                font-size: 18px;
            }
            .solicitud-item {
                flex-direction: column;
                align-items: stretch;
                padding: 15px;
                gap: 12px;
            }
            .solicitud-item > div:last-child,
            .btn-detalles,
            .btn-cancelar-turno {
                width: 100%;
                text-align: center;
            }
            .acciones-form-reserva {
                flex-direction: column;
            }
            .fc .fc-toolbar {
                flex-direction: column;
                gap: 8px;
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
            <!-- Contenedor de mensajes de éxito o error al reservar -->
            <div id="alerta-cliente" class="alerta"></div>

            <!-- Botonera de 3 pestañas -->
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
                                <h3>{{ ucfirst($solicitud->area) }}</h3>
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
                    <h2>Reservar Turno (Bloques de 2 horas)</h2>
                    <p style="font-size: 14px; color: #555555; margin-bottom: 18px;">
                        Seleccioná un bloque horario disponible (en verde) para coordinar tu entrevista con <strong>{{ $abogado->name ?? 'el profesional' }}</strong>.
                    </p>

                    <!-- Formulario que se despliega al hacer clic sobre un horario libre -->
                    <div id="panel-confirmar-reserva" class="panel-reserva">
                        <h3>Confirmar reserva de turno</h3>
                        <form id="form-reservar-turno">
                            <input type="hidden" name="abogado_id" id="abogado_id" value="{{ $abogado->id ?? '' }}">
                            <input type="hidden" name="inicio" id="inicio_turno" required>

                            <div class="form-reserva-grid">
                                <div class="form-group">
                                    <label for="fecha_visual">Fecha seleccionada</label>
                                    <input type="text" id="fecha_visual" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="hora_visual">Bloque horario (2 hs)</label>
                                    <input type="text" id="hora_visual" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="modalidad_turno">Modalidad</label>
                                    <select name="modalidad" id="modalidad_turno" required>
                                        <option value="virtual">Virtual (Videollamada Jitsi)</option>
                                        <option value="presencial">Presencial en el estudio</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <label for="motivo_turno">Motivo de la consulta</label>
                                <textarea name="motivo" id="motivo_turno" rows="2" maxlength="1000" placeholder="Detallá brevemente el tema a tratar en la reunión..."></textarea>
                            </div>

                            <div class="acciones-form-reserva">
                                <button type="submit" id="btn-confirmar-reserva" class="btn-detalles">Confirmar Turno</button>
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

                    <div id="lista-turnos-cliente">
                        @forelse ($turnos ?? [] as $turno)
                            @php
                                $inicioTurno = \Carbon\Carbon::parse($turno->inicio);
                                $finTurno = \Carbon\Carbon::parse($turno->fin);
                            @endphp
                            <div class="solicitud-item">
                                <div class="solicitud-info">
                                    <h3>Consulta con {{ $turno->abogado->name ?? 'Abogado' }}</h3>
                                    <p style="font-size: 14px; color: #555555; margin-bottom: 6px;">
                                        Fecha: <strong>{{ $inicioTurno->format('d/m/Y') }}</strong> ·
                                        Horario: <strong>{{ $inicioTurno->format('H:i') }} a {{ $finTurno->format('H:i') }} hs</strong> ·
                                        Modalidad: <strong>{{ ucfirst($turno->modalidad) }}</strong>
                                    </p>
                                    @if ($turno->motivo)
                                        <p style="font-size: 13px; color: #666666; margin-bottom: 8px;">
                                            Motivo: {{ $turno->motivo }}
                                        </p>
                                    @endif
                                    <span class="estado-tag estado-{{ strtolower($turno->estado) }}">
                                        {{ ucfirst($turno->estado) }}
                                    </span>
                                </div>

                                <div style="display: flex; gap: 10px; align-items: center;">
                                    @if ($turno->modalidad === 'virtual' && $turno->estado === 'reservado')
                                        <a href="/turnos/{{ $turno->id }}/videollamada" class="btn-detalles">
                                            Sala de reunión
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p style="color: #777777;">Todavía no tenés turnos agendados. Podés reservar uno desde la pestaña "Reservar Turno".</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        Estudio Jurídico · Atención con cita previa
    </footer>

    <script>
        const ABOGADO_ID = @json($abogado->id ?? null);
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let calendarioCliente = null;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendario-cliente');
            if (!calendarEl) return;

            calendarioCliente = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                slotMinTime: '08:00:00',
                slotMaxTime: '21:00:00',
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
                events: async function(info, successCallback, failureCallback) {
                    if (!ABOGADO_ID) {
                        successCallback([]);
                        return;
                    }

                    const formatearFecha = (d) => {
                        const anio = d.getFullYear();
                        const mes = String(d.getMonth() + 1).padStart(2, '0');
                        const dia = String(d.getDate()).padStart(2, '0');
                        return `${anio}-${mes}-${dia}`;
                    };

                    const desde = formatearFecha(info.start);
                    const finTemp = new Date(info.end);
                    finTemp.setDate(finTemp.getDate() - 1);
                    const hasta = formatearFecha(finTemp);

                    try {
                        const url = `/abogado/horarios-libres-rango?desde=${desde}&hasta=${hasta}&abogado_id=${ABOGADO_ID}`;
                        const res = await fetch(url, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!res.ok) {
                            successCallback([]);
                            return;
                        }

                        const data = await res.json();
                        const eventos = [];

                        Object.entries(data.dias || {}).forEach(([fechaStr, bloques]) => {
                            bloques.forEach(bloque => {
                                eventos.push({
                                    title: `${bloque.hora_inicio} - ${bloque.hora_fin}`,
                                    start: `${fechaStr}T${bloque.hora_inicio}:00`,
                                    end: `${fechaStr}T${bloque.hora_fin}:00`,
                                    color: '#137333',
                                    extendedProps: {
                                        fecha: fechaStr,
                                        hora_inicio: bloque.hora_inicio,
                                        hora_fin: bloque.hora_fin
                                    }
                                });
                            });
                        });

                        successCallback(eventos);
                    } catch (error) {
                        console.error('Error cargando horarios libres:', error);
                        failureCallback(error);
                    }
                },
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    document.getElementById('inicio_turno').value = `${props.fecha} ${props.hora_inicio}:00`;
                    document.getElementById('fecha_visual').value = props.fecha;
                    document.getElementById('hora_visual').value = `${props.hora_inicio} a ${props.hora_fin} hs`;

                    const panel = document.getElementById('panel-confirmar-reserva');
                    panel.style.display = 'block';
                    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            });

            calendarioCliente.render();

            // Envío asíncrono del formulario de reserva a POST /turnos
            const formReserva = document.getElementById('form-reservar-turno');
            formReserva.addEventListener('submit', async function(e) {
                e.preventDefault();

                const btnSubmit = document.getElementById('btn-confirmar-reserva');
                btnSubmit.disabled = true;
                btnSubmit.textContent = 'Reservando...';

                const payload = {
                    abogado_id: parseInt(document.getElementById('abogado_id').value, 10),
                    inicio: document.getElementById('inicio_turno').value,
                    modalidad: document.getElementById('modalidad_turno').value,
                    motivo: document.getElementById('motivo_turno').value.trim() || null
                };

                try {
                    const respuesta = await fetch('/turnos', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await respuesta.json();

                    if (!respuesta.ok) {
                        const primerError = data.errors
                            ? Object.values(data.errors).flat()[0]
                            : (data.mensaje || 'No se pudo reservar el turno.');
                        mostrarAlerta(primerError, 'error');
                    } else {
                        mostrarAlerta(data.mensaje || 'Turno reservado correctamente.', 'exito');
                        cerrarPanelReserva();
                        formReserva.reset();
                        document.getElementById('abogado_id').value = ABOGADO_ID;

                        calendarioCliente.refetchEvents();
                        await actualizarListaTurnosCliente();
                    }
                } catch (err) {
                    mostrarAlerta('Ocurrió un error de conexión al intentar reservar.', 'error');
                } finally {
                    btnSubmit.disabled = false;
                    btnSubmit.textContent = 'Confirmar Turno';
                }
            });
        });

        async function actualizarListaTurnosCliente() {
            try {
                const res = await fetch('/turnos', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;

                const turnos = await res.json();
                const contenedor = document.getElementById('lista-turnos-cliente');

                if (!turnos.length) {
                    contenedor.innerHTML = '<p style="color: #777777;">Todavía no tenés turnos agendados. Podés reservar uno desde la pestaña "Reservar Turno".</p>';
                    return;
                }

                contenedor.innerHTML = turnos.map(t => {
                    const inicio = new Date(t.inicio);
                    const fin = new Date(t.fin);
                    const fechaStr = inicio.toLocaleDateString('es-AR');
                    const horaIni = inicio.toTimeString().slice(0, 5);
                    const horaFin = fin.toTimeString().slice(0, 5);
                    const abogadoNombre = t.abogado ? t.abogado.name : 'Abogado';
                    const modalidadCap = t.modalidad.charAt(0).toUpperCase() + t.modalidad.slice(1);
                    const estadoCap = t.estado.charAt(0).toUpperCase() + t.estado.slice(1);

                    const botonJitsi = (t.modalidad === 'virtual' && t.estado === 'reservado')
                        ? `<a href="/turnos/${t.id}/videollamada" class="btn-detalles">Sala de reunión</a>`
                        : '';

                    const motivoHtml = t.motivo
                        ? `<p style="font-size: 13px; color: #666666; margin-bottom: 8px;">Motivo: ${escapeHtml(t.motivo)}</p>`
                        : '';

                    return `
                        <div class="solicitud-item">
                            <div class="solicitud-info">
                                <h3>Consulta con ${escapeHtml(abogadoNombre)}</h3>
                                <p style="font-size: 14px; color: #555555; margin-bottom: 6px;">
                                    Fecha: <strong>${fechaStr}</strong> ·
                                    Horario: <strong>${horaIni} a ${horaFin} hs</strong> ·
                                    Modalidad: <strong>${modalidadCap}</strong>
                                </p>
                                ${motivoHtml}
                                <span class="estado-tag estado-${t.estado.toLowerCase()}">
                                    ${estadoCap}
                                </span>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                ${botonJitsi}
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (e) {
                console.error('Error actualizando turnos del cliente:', e);
            }
        }

        function mostrarAlerta(texto, tipo) {
            const alerta = document.getElementById('alerta-cliente');
            alerta.textContent = texto;
            alerta.className = 'alerta ' + (tipo === 'exito' ? 'alerta-exito' : 'alerta-error');
            alerta.style.display = 'block';
            alerta.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function escapeHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto;
            return div.innerHTML;
        }

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