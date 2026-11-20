<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil Abogado | Estudio Jurídico</title>
    
    <!-- Librería FullCalendar v6 -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/es.global.min.js"></script>

    <style>
        /* Esto quita los bordes blancos de los costados y devuelve la tipografía limpia */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #f4f3ef; color: #2c3e50; display: flex; flex-direction: column; min-height: 100vh; }
        
        /* Encabezado original */
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

        /* Personalización de botones de FullCalendar para que combinen con el estudio */
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
            font-size: 1.25rem !important;
            text-transform: capitalize;
        }

        .solicitud-item {
            background-color: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .btn-detalles:hover {
            background-color: #122a33;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #1a3b47;
        }

        .form-group input,
        .form-group select {
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

        .btn-guardar:hover {
            background-color: #122a33;
        }

        .btn-bloquear {
            background-color: #c5221f;
        }

        .btn-bloquear:hover {
            background-color: #a11917;
        }

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
        <!-- Pestañas del panel del abogado - FRE -->
        <div class="tabs-nav">
            <button type="button" class="tab-btn active"
                onclick="cambiarPestana(event, 'tab-consultas')">
                Consultas
            </button>

            <button type="button" class="tab-btn"
                onclick="cambiarPestana(event, 'tab-agenda')">
                Agenda
            </button>

            <button type="button" class="tab-btn"
                onclick="cambiarPestana(event, 'tab-disponibilidad')">
                Disponibilidad
            </button>

            <button type="button" class="tab-btn"
                onclick="cambiarPestana(event, 'tab-bloqueos')">
                Fechas
            </button>
        </div>

        <!-- Acceso a la configuración de agenda - MALE -->
        <div class="card">
            <h2>Configuración de agenda</h2>

            <p style="margin-bottom: 20px;">
                Configurá tus horarios de atención y bloqueos de agenda.
            </p>

            <a href="{{ route('abogado.agenda') }}" class="btn-detalles">
                Configurar mi agenda
            </a>
        </div>

        <!-- Contenido de las pestañas - FRE -->
        @include('perfiles.pestanias.consultas')
        @include('perfiles.pestanias.agenda')
        @include('perfiles.pestanias.disponibilidad')
        @include('perfiles.pestanias.bloqueos')
    </main>

    <footer>
        Estudio Jurídico · Panel Profesional
    </footer>

    <script>
        let calendarioAbogado = null;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendario-abogado');
            if (!calendarEl) return;
            
            calendarioAbogado = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
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
                // Datos simulados 
                // Luego reemplazar por: events: '/agenda/disponibilidad'
                events: [
                    {
                        title: 'Turno: Juan Pérez (Civil)',
                        start: '2026-11-16T10:00:00',
                        end: '2026-11-16T11:00:00',
                        color: '#1a3b47',
                        url: '/consultas/abogado/1'
                    },
                    {
                        title: 'Turno: María Gómez (Familia)',
                        start: '2026-11-18T15:00:00',
                        end: '2026-11-18T16:00:00',
                        color: '#1a3b47',
                        url: '/consultas/abogado/2'
                    },
                    {
                        title: 'Día Bloqueado (Audiencia)',
                        start: '2026-11-20',
                        allDay: true,
                        color: '#c5221f'
                    }
                ],
                eventClick: function(info) {
                    // Si el evento tiene URL, navega hacia el expediente de la consulta
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        window.location.href = info.event.url;
                    }
                }
            });

            calendarioAbogado.render();
        });

        function cambiarPestana(evento, idPestana) {
            // Oculta todas las pestañas y quita la clase activa de los botones
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            
            // Muestra la pestaña seleccionada y resalta su botón
            document.getElementById(idPestana).classList.add('active');
            evento.currentTarget.classList.add('active');

            // Recalcula el tamaño del calendario al abrir la pestaña Mi Agenda
            if (idPestana === 'tab-agenda' && calendarioAbogado) {
                setTimeout(() => {
                    calendarioAbogado.updateSize();
                }, 50);
            }
        }
    </script>

</body>
</html>