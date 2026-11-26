<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Perfil Abogado | Estudio Jurídico</title>
    
    <!-- Librería FullCalendar v6 -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #f4f3ef; color: #2c3e50; display: flex; flex-direction: column; min-height: 100vh; }
        
        /* Encabezado */
        header { background-color: #1a3b47; color: #ffffff; padding: 25px 40px; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 26px; font-weight: 600; }
        
        /* Botones del encabezado (Mi Perfil y Cerrar sesión) */
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
        }
        .btn-logout:hover { background-color: rgba(255, 255, 255, 0.1); }

        main {
            max-width: 1150px;
            width: 100%;
            margin: 35px auto;
            padding: 0 25px;
            flex-grow: 1;
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

        .alerta-abogado {
            display: none;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 18px;
        }
        .alerta-abogado.exito {
            background-color: #e6f4ea;
            border: 1px solid #ceead6;
            color: #137333;
        }
        .alerta-abogado.error {
            background-color: #fce8e6;
            border: 1px solid #fad2cf;
            color: #c5221f;
        }

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
            padding: 12px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            flex: 1;
            text-align: center;
            min-width: 150px;
        }

        .tab-btn:hover { background-color: #d8d5cc; }
        .tab-btn.active { background-color: #1a3b47; color: #ffffff; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Personalización de botones de FullCalendar */
        .fc .fc-button-primary { background-color: #1a3b47; border-color: #1a3b47; }
        .fc .fc-button-primary:hover { background-color: #122a33; border-color: #122a33; }
        .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #0d1f26; border-color: #0d1f26; }
        .fc-toolbar-title { color: #1a3b47; font-size: 1.25rem !important; text-transform: capitalize; }
        .fc-event { cursor: pointer; }

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

        .solicitud-info h3 { color: #1a3b47; font-size: 18px; margin-bottom: 6px; }

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
        .btn-detalles:hover { background-color: #122a33; }

        .btn-cancelar-turno {
            background-color: transparent;
            color: #c5221f;
            border: 1px solid #c5221f;
            padding: 8px 14px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-cancelar-turno:hover { background-color: #fce8e6; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 14px; font-weight: 600; color: #1a3b47; }
        .form-group input, .form-group select {
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fafafa;
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
        }
        .btn-guardar:hover { background-color: #122a33; }

        .btn-bloquear { background-color: #c5221f; }
        .btn-bloquear:hover { background-color: #a11917; }

        .lista-config {
            margin-top: 20px;
            border-top: 1px solid #eeeeee;
            padding-top: 15px;
        }

        .item-config {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 14px;
            gap: 12px;
            flex-wrap: wrap;
        }

        footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #777777;
            margin-top: auto;
        }

        @media (max-width: 768px) {
    header {
        padding: 18px 15px;
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    header h1 { font-size: 22px; }
    main {
        padding: 0 12px;
        margin: 15px auto;
        max-width: 100%;
    }
    .card { padding: 16px; }
    .card h2 { font-size: 18px; }
    .tabs-nav {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .tab-btn {
        min-width: 0;
        width: 100%;
        padding: 10px 8px;
        font-size: 13px;
    }
    .solicitud-item,
    .item-config {
        flex-direction: column;
        align-items: stretch;
        padding: 15px;
    }
    .btn-detalles,
    .btn-cancelar-turno,
    .btn-guardar {
        width: 100%;
        text-align: center;
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
        <h1>Panel del Abogado</h1>
        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="/abogado/perfil" class="btn-logout">Mi Perfil</a>
            <form action="/logout" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>

    <main>
        <div id="alerta-abogado" class="alerta-abogado"></div>

        <!-- Pestañas del panel del abogado -->
        <div class="tabs-nav">
            <button type="button" class="tab-btn active" onclick="cambiarPestana(event, 'tab-consultas')">
                Consultas
            </button>

            <button type="button" class="tab-btn" onclick="cambiarPestana(event, 'tab-agenda')">
                Agenda
            </button>

            <button type="button" class="tab-btn" onclick="cambiarPestana(event, 'tab-disponibilidad')">
                Disponibilidad
            </button>

            <button type="button" class="tab-btn" onclick="cambiarPestana(event, 'tab-bloqueos')">
                Fechas
            </button>
        </div>

        <!-- Contenido de las pestañas -->
        @include('perfiles.pestanias.consultas')
        @include('perfiles.pestanias.agenda')
        @include('perfiles.pestanias.disponibilidad')
        @include('perfiles.pestanias.bloqueos')
    </main>

    <footer>
        Estudio Jurídico · Panel Profesional
    </footer>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let calendarioAbogado = null;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendario-abogado');
            if (!calendarEl) return;

            calendarioAbogado = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                slotMinTime: '08:00:00',
                slotMaxTime: '21:00:00',
                allDaySlot: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },
                // Carga real de turnos (GET /turnos) y bloqueos (GET /abogado/bloqueos)
                events: async function(info, successCallback, failureCallback) {
                    try {
                        const [resTurnos, resBloqueos] = await Promise.all([
                            fetch('/turnos', { headers: { 'Accept': 'application/json' } }),
                            fetch('/abogado/bloqueos', { headers: { 'Accept': 'application/json' } })
                        ]);

                        const dataTurnos = resTurnos.ok ? await resTurnos.json() : [];
                        const dataBloqueos = resBloqueos.ok ? await resBloqueos.json() : [];

                        const turnos = Array.isArray(dataTurnos) ? dataTurnos : (dataTurnos.turnos || []);
                        const bloqueos = Array.isArray(dataBloqueos) ? dataBloqueos : (dataBloqueos.bloqueos || []);
                        const eventos = [];

                        // 1. Mapear turnos reales (reservados vs cancelados)
                        turnos.forEach(turno => {
                            const clienteNombre = turno.cliente ? turno.cliente.name : 'Cliente';
                            const esCancelado = turno.estado === 'cancelado';

                            eventos.push({
                                id: 'turno-' + turno.id,
                                title: `${esCancelado ? '[CANCELADO]' : 'Turno:'} ${clienteNombre} (${turno.modalidad})`,
                                start: turno.inicio,
                                end: turno.fin,
                                color: esCancelado ? '#7f8c8d' : '#1a3b47',
                                extendedProps: {
                                    tipo: 'turno',
                                    turno: turno
                                }
                            });
                        });

                        // 2. Mapear bloqueos reales (fecha completa u horario específico)
                        bloqueos.forEach(bloqueo => {
                            const fechaSolo = bloqueo.fecha.split('T')[0];
                            const motivo = bloqueo.motivo ? ` (${bloqueo.motivo})` : '';

                            if (!bloqueo.hora_inicio || !bloqueo.hora_fin) {
                                eventos.push({
                                    id: 'bloqueo-' + bloqueo.id,
                                    title: `Bloqueado${motivo}`,
                                    start: fechaSolo,
                                    allDay: true,
                                    color: '#c5221f',
                                    extendedProps: { tipo: 'bloqueo' }
                                });
                            } else {
                                eventos.push({
                                    id: 'bloqueo-' + bloqueo.id,
                                    title: `Bloqueado${motivo}`,
                                    start: `${fechaSolo}T${bloqueo.hora_inicio}`,
                                    end: `${fechaSolo}T${bloqueo.hora_fin}`,
                                    color: '#c5221f',
                                    extendedProps: { tipo: 'bloqueo' }
                                });
                            }
                        });

                        if (typeof renderizarListaTurnosAbogado === 'function') {
                            renderizarListaTurnosAbogado(turnos);
                        }

                        successCallback(eventos);
                    } catch (error) {
                        console.error('Error cargando agenda del abogado:', error);
                        failureCallback(error);
                    }
                },
                eventClick: function(info) {
                    const props = info.event.extendedProps;
                    if (props.tipo === 'turno' && props.turno) {
                        if (typeof mostrarDetalleTurnoAbogado === 'function') {
                            mostrarDetalleTurnoAbogado(props.turno);
                        } else if (props.turno.estado !== 'cancelado' && props.turno.modalidad === 'virtual') {
                            window.location.href = `/turnos/${props.turno.id}/videollamada`;
                        }
                    }
                }
            });

            calendarioAbogado.render();
            if (typeof cargarDisponibilidades === 'function') cargarDisponibilidades();
            if (typeof cargarBloqueos === 'function') cargarBloqueos();
        });

        let temporizadorAlertaAbogado = null;
        function mostrarAlertaAbogado(texto, tipo = 'exito') {
            const alerta = document.getElementById('alerta-abogado');
            if (!alerta) return;

            alerta.textContent = texto;
            alerta.className = `alerta-abogado ${tipo}`;
            alerta.style.display = 'block';

            clearTimeout(temporizadorAlertaAbogado);
            temporizadorAlertaAbogado = setTimeout(() => {
                alerta.style.display = 'none';
            }, 3000);
        }

        function escapeHtml(texto) {
            const div = document.createElement('div');
            div.textContent = texto ?? '';
            return div.innerHTML;
        }

        function cambiarPestana(evento, idPestana) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            
            document.getElementById(idPestana).classList.add('active');
            evento.currentTarget.classList.add('active');

            if (idPestana === 'tab-agenda' && calendarioAbogado) {
                setTimeout(() => {
                    calendarioAbogado.updateSize();
                    calendarioAbogado.refetchEvents();
                }, 50);
            }
        }

    </script>

</body>
</html>