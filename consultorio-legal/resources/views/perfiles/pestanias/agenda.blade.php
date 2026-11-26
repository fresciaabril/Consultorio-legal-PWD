<div id="tab-agenda" class="tab-content">
    <div class="card">
        <div class="agenda-header">
            <h2>Mi Agenda y Turnos</h2>
            <div class="agenda-leyenda">
                <span><strong class="leyenda-reservado">■</strong> Turno Reservado</span>
                <span><strong class="leyenda-cancelado">■</strong> Turno Cancelado</span>
                <span><strong class="leyenda-bloqueado">■</strong> Bloqueado</span>
            </div>
        </div>

        <!-- Panel detalle al hacer clic en un turno del calendario -->
        <div id="detalle-turno-seleccionado" class="detalle-turno-panel"></div>

        <!-- Contenedor donde se dibuja FullCalendar -->
        <div id="calendario-abogado"></div>

        <!-- Listado de turnos agendados del día (solo pendientes de realizarse, sin cancelados) -->
        <div class="lista-config lista-turnos-wrapper">
            <h3 class="lista-turnos-titulo">Turnos agendados para hoy ({{ now()->format('d/m/Y') }})</h3>

            <div id="lista-turnos-abogado">
                @php
                    $ahora = now();
                    $turnosHoyPendientes = collect($turnos ?? [])->filter(function ($t) use ($ahora) {
                        $ini = \Carbon\Carbon::parse($t->inicio);
                        $fin = \Carbon\Carbon::parse($t->fin);
                        return $t->estado === 'reservado'
                            && $ini->isToday()
                            && $fin->gt($ahora);
                    });
                @endphp

                @forelse ($turnosHoyPendientes as $turno)
                    @php
                        $inicio = \Carbon\Carbon::parse($turno->inicio);
                        $fin = \Carbon\Carbon::parse($turno->fin);
                        $puedeCancelar = $ahora->lte($inicio->copy()->subDay());
                        $habilitadoDesde = $inicio->copy()->subMinutes(10);
                        $habilitadoHasta = $inicio->copy()->addMinutes(90);
                        $llamadaHabilitada = $ahora->between($habilitadoDesde, $habilitadoHasta);
                    @endphp
                    <div class="item-config" id="turno-abogado-{{ $turno->id }}">
                        <div>
                            <strong>{{ $turno->cliente->name ?? 'Cliente' }}</strong>
                            <span class="texto-detalle-secundario">
                                — {{ $inicio->format('d/m/Y') }} de {{ $inicio->format('H:i') }} a {{ $fin->format('H:i') }} hs
                                ({{ ucfirst($turno->modalidad) }})
                            </span>
                            @if (!empty($turno->motivo))
                                <div class="texto-motivo">Motivo: {{ $turno->motivo }}</div>
                            @endif
                        </div>

                        <div class="turno-acciones">
                            <span class="turno-estado-ok">Reservado</span>

                            @if ($turno->modalidad === 'virtual')
                                @if ($llamadaHabilitada)
                                    <a href="/turnos/{{ $turno->id }}/videollamada" class="btn-detalles btn-sm">
                                        Sala de reunión
                                    </a>
                                @else
                                    <div class="sala-bloque-deshabilitado">
                                        <span class="aviso-sala-tiempo">La reunión se habilitará 10 min antes del inicio</span>
                                        <button type="button" disabled class="btn-sala-deshabilitada btn-sm">
                                            Sala de reunión
                                        </button>
                                    </div>
                                @endif
                            @endif

                            @if ($puedeCancelar)
                                <button type="button"
                                        class="btn-cancelar-turno btn-sm"
                                        onclick="cancelarTurnoAbogado({{ $turno->id }})">
                                    Cancelar turno
                                </button>
                            @else
                                <span class="texto-plazo-cerrado">Plazo de cancelación cerrado (menos de 24 hs)</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="texto-vacio">No hay turnos pendientes para el día de hoy.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function parsearFechaHoraExacta(valor) {
        if (!valor) return new Date();
        if (typeof valor === 'string' && valor.endsWith('Z')) {
            return new Date(valor);
        }
        const limpio = String(valor).replace('T', ' ').slice(0, 19);
        const [fechaParte, horaParte] = limpio.split(' ');
        const [anio, mes, dia] = fechaParte.split('-').map(Number);
        const [hora, min, seg] = (horaParte || '00:00:00').split(':').map(Number);
        return new Date(anio, mes - 1, dia, hora, min, seg || 0);
    }

    function renderizarListaTurnosAbogado(turnos) {
        const contenedor = document.getElementById('lista-turnos-abogado');
        if (!contenedor) return;

        const ahora = new Date();
        const hoyAnio = ahora.getFullYear();
        const hoyMes = ahora.getMonth();
        const hoyDia = ahora.getDate();

        const turnosHoy = (turnos || []).filter(t => {
            if (t.estado !== 'reservado') return false;
            const inicio = parsearFechaHoraExacta(t.inicio);
            const fin = parsearFechaHoraExacta(t.fin);
            const esHoy = inicio.getFullYear() === hoyAnio
                && inicio.getMonth() === hoyMes
                && inicio.getDate() === hoyDia;
            return esHoy && fin > ahora;
        });

        if (!turnosHoy.length) {
            contenedor.innerHTML = '<p class="texto-vacio">No hay turnos pendientes para el día de hoy.</p>';
            return;
        }

        contenedor.innerHTML = turnosHoy.map(t => {
            const inicio = parsearFechaHoraExacta(t.inicio);
            const fin = parsearFechaHoraExacta(t.fin);
            const fechaStr = inicio.toLocaleDateString('es-AR');
            const horaIni = String(inicio.getHours()).padStart(2, '0') + ':' + String(inicio.getMinutes()).padStart(2, '0');
            const horaFin = String(fin.getHours()).padStart(2, '0') + ':' + String(fin.getMinutes()).padStart(2, '0');
            const clienteNombre = t.cliente ? t.cliente.name : 'Cliente';
            const clienteEmail = t.cliente ? t.cliente.email : '';

            const limiteCancelacion = new Date(inicio.getTime() - (24 * 60 * 60 * 1000));
            const puedeCancelar = ahora <= limiteCancelacion;

            const habilitadoDesde = new Date(inicio.getTime() - (10 * 60 * 1000));
            const habilitadoHasta = new Date(inicio.getTime() + (90 * 60 * 1000));
            const llamadaHabilitada = ahora >= habilitadoDesde && ahora <= habilitadoHasta;

            let btnJitsi = '';
            if (t.modalidad === 'virtual') {
                if (llamadaHabilitada) {
                    btnJitsi = `<a href="/turnos/${t.id}/videollamada" class="btn-detalles">Sala de reunión</a>`;
                } else {
                    btnJitsi = `
                        <div class="sala-bloque-deshabilitado">
                            <span class="aviso-sala-tiempo">La reunión se habilitará 10 min antes del inicio</span>
                            <button type="button" disabled class="btn-sala-deshabilitada">Sala de reunión</button>
                        </div>
                    `;
                }
            }

            const btnCancelar = puedeCancelar
                ? `<button type="button" class="btn-cancelar-turno" onclick="cancelarTurnoAbogado(${t.id})">Cancelar turno</button>`
                : `<span class="texto-plazo-cerrado">Plazo de cancelación cerrado (menos de 24 hs)</span>`;

            const motivoHtml = t.motivo
                ? `<p class="texto-motivo">Motivo: ${escapeHtml(t.motivo)}</p>`
                : '';

            return `
                <div class="solicitud-item">
                    <div class="solicitud-info">
                        <h3>${escapeHtml(clienteNombre)} <span class="solicitud-email">(${escapeHtml(clienteEmail)})</span></h3>
                        <p class="solicitud-meta">
                            Fecha: <strong>${fechaStr}</strong> ·
                            Horario: <strong>${horaIni} a ${horaFin} hs</strong> ·
                            Modalidad: <strong>${t.modalidad.charAt(0).toUpperCase() + t.modalidad.slice(1)}</strong>
                        </p>
                        ${motivoHtml}
                        <span class="estado-tag estado-${t.estado.toLowerCase()}">
                            ${t.estado.toUpperCase()}
                        </span>
                    </div>
                    <div class="turno-acciones">
                        ${btnJitsi}
                        ${btnCancelar}
                    </div>
                </div>
            `;
        }).join('');
    }

    function mostrarDetalleTurnoAbogado(t) {
        const panel = document.getElementById('detalle-turno-seleccionado');
        const ahora = new Date();
        const inicio = parsearFechaHoraExacta(t.inicio);
        const fin = parsearFechaHoraExacta(t.fin);
        const fechaStr = inicio.toLocaleDateString('es-AR');
        const horaIni = String(inicio.getHours()).padStart(2, '0') + ':' + String(inicio.getMinutes()).padStart(2, '0');
        const horaFin = String(fin.getHours()).padStart(2, '0') + ':' + String(fin.getMinutes()).padStart(2, '0');
        const clienteNombre = t.cliente ? t.cliente.name : 'Cliente';
        const esReservado = t.estado === 'reservado';

        const limiteCancelacion = new Date(inicio.getTime() - (24 * 60 * 60 * 1000));
        const puedeCancelar = esReservado && (ahora <= limiteCancelacion);

        const habilitadoDesde = new Date(inicio.getTime() - (10 * 60 * 1000));
        const habilitadoHasta = new Date(inicio.getTime() + (90 * 60 * 1000));
        const llamadaHabilitada = esReservado && ahora >= habilitadoDesde && ahora <= habilitadoHasta;

        let btnJitsi = '';
        if (t.modalidad === 'virtual' && esReservado) {
            if (llamadaHabilitada) {
                btnJitsi = `<a href="/turnos/${t.id}/videollamada" class="btn-detalles">Sala de reunión</a>`;
            } else {
                btnJitsi = `
                    <div class="sala-bloque-deshabilitado">
                        <span class="aviso-sala-tiempo">La reunión se habilitará 10 min antes del inicio</span>
                        <button type="button" disabled class="btn-sala-deshabilitada">Sala de reunión</button>
                    </div>
                `;
            }
        }

        const btnCancelar = puedeCancelar
            ? `<button type="button" class="btn-cancelar-turno" onclick="cancelarTurnoAbogado(${t.id})">Cancelar turno</button>`
            : (esReservado ? `<span class="texto-plazo-cerrado">Plazo de cancelación cerrado (menos de 24 hs)</span>` : '');

        panel.innerHTML = `
            <div class="detalle-turno-contenido">
                <div>
                    <h3 class="detalle-turno-titulo">
                        Turno con ${escapeHtml(clienteNombre)} · ${fechaStr} (${horaIni} - ${horaFin} hs)
                    </h3>
                    <p class="detalle-turno-sub">
                        Modalidad: <strong>${t.modalidad}</strong> · Estado: <strong>${t.estado}</strong>
                        ${t.motivo ? `· Motivo: ${escapeHtml(t.motivo)}` : ''}
                    </p>
                </div>
                <div class="turno-acciones">
                    ${btnJitsi}
                    ${btnCancelar}
                    <button type="button" class="btn-cerrar-detalle" onclick="document.getElementById('detalle-turno-seleccionado').style.display='none'">Cerrar</button>
                </div>
            </div>
        `;
        panel.style.display = 'block';
    }

    async function cancelarTurnoAbogado(turnoId) {
        if (!confirm('¿Confirmás que querés cancelar este turno? El horario volverá a quedar libre.')) {
            return;
        }

        try {
            const res = await fetch(`/turnos/${turnoId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });
            const data = await res.json();

            if (!res.ok) {
                mostrarAlertaAbogado(data.mensaje || 'No se pudo cancelar el turno.', 'error');
                return;
            }

            mostrarAlertaAbogado(data.mensaje || 'Turno cancelado correctamente.', 'exito');
            document.getElementById('detalle-turno-seleccionado').style.display = 'none';
            if (calendarioAbogado) {
                calendarioAbogado.refetchEvents();
            }
        } catch (e) {
            mostrarAlertaAbogado('Error de conexión al cancelar el turno.', 'error');
        }
    }
</script>