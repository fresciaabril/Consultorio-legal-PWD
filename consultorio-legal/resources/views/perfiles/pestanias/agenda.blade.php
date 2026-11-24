<div id="tab-agenda" class="tab-content">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eeeeee; padding-bottom: 10px; flex-wrap: wrap; gap: 10px;">
            <h2 style="border: none; margin: 0; padding: 0;">Mi Agenda y Turnos</h2>
            <div style="display: flex; gap: 15px; font-size: 13px;">
                <span><strong style="color: #1a3b47;">■</strong> Turno Reservado</span>
                <span><strong style="color: #7f8c8d;">■</strong> Turno Cancelado</span>
                <span><strong style="color: #c5221f;">■</strong> Bloqueado</span>
            </div>
        </div>

        <!-- Contenedor donde se dibuja FullCalendar -->
        <div id="calendario-abogado" style="min-height: 550px;"></div>

        <!-- Listado de turnos reales con acción de cancelar -->
        <div class="lista-config" style="margin-top: 25px;">
            <h3 style="font-size: 16px; color: #1a3b47; margin-bottom: 12px;">Listado de turnos agendados</h3>

            <div id="lista-turnos-abogado">
                @forelse ($turnos ?? [] as $turno)
                    @php
                        $inicio = \Carbon\Carbon::parse($turno->inicio);
                        $fin = \Carbon\Carbon::parse($turno->fin);
                    @endphp
                    <div class="item-config" id="turno-abogado-{{ $turno->id }}" style="flex-wrap: wrap; gap: 10px;">
                        <div>
                            <strong>{{ $turno->cliente->name ?? 'Cliente' }}</strong>
                            <span style="color: #555;">
                                — {{ $inicio->format('d/m/Y') }} de {{ $inicio->format('H:i') }} a {{ $fin->format('H:i') }} hs
                                ({{ ucfirst($turno->modalidad) }})
                            </span>
                            @if (!empty($turno->motivo))
                                <div style="font-size: 13px; color: #666; margin-top: 3px;">Motivo: {{ $turno->motivo }}</div>
                            @endif
                        </div>

                        <div style="display: flex; align-items: center; gap: 10px;">
                            @if ($turno->estado === 'cancelado')
                                <span style="color: #7f8c8d; font-weight: 600; font-size: 13px;">Cancelado</span>
                            @else
                                <span style="color: #137333; font-weight: 600; font-size: 13px;">Reservado</span>

                                @if ($turno->modalidad === 'virtual')
                                    <a href="/turnos/{{ $turno->id }}/videollamada" class="btn-detalles" style="padding: 6px 12px; font-size: 12px;">
                                        Entrar a sala
                                    </a>
                                @endif

                                <button type="button"
                                        onclick="cancelarTurnoAbogado({{ $turno->id }})"
                                        style="background-color: #c5221f; color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer;">
                                    Cancelar turno
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="color: #777; font-size: 14px;">Todavía no hay turnos registrados.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function renderizarListaTurnosAbogado(turnos) {
        const contenedor = document.getElementById('lista-turnos-abogado');
        if (!contenedor) return;

        if (!turnos.length) {
            contenedor.innerHTML = '<p style="color: #777777;">Todavía no hay turnos registrados.</p>';
            return;
        }

        contenedor.innerHTML = turnos.map(t => {
            const inicio = new Date(t.inicio);
            const fin = new Date(t.fin);
            const fechaStr = inicio.toLocaleDateString('es-AR');
            const horaIni = inicio.toTimeString().slice(0, 5);
            const horaFin = fin.toTimeString().slice(0, 5);
            const clienteNombre = t.cliente ? t.cliente.name : 'Cliente';
            const clienteEmail = t.cliente ? t.cliente.email : '';
            const esReservado = t.estado === 'reservado';
            const esFuturo = inicio > new Date();

            const btnJitsi = (t.modalidad === 'virtual' && esReservado)
                ? `<a href="/turnos/${t.id}/videollamada" class="btn-detalles">Sala de reunión</a>`
                : '';

            const btnCancelar = (esReservado && esFuturo)
                ? `<button type="button" class="btn-cancelar-turno" onclick="cancelarTurnoAbogado(${t.id})">Cancelar turno</button>`
                : '';

            const motivoHtml = t.motivo
                ? `<p style="font-size: 13px; color: #666666; margin-bottom: 8px;">Motivo: ${escapeHtml(t.motivo)}</p>`
                : '';

            return `
                <div class="solicitud-item">
                    <div class="solicitud-info">
                        <h3>${escapeHtml(clienteNombre)} <span style="font-size: 13px; font-weight: 400; color: #666;">(${escapeHtml(clienteEmail)})</span></h3>
                        <p style="font-size: 14px; color: #555555; margin-bottom: 6px;">
                            Fecha: <strong>${fechaStr}</strong> ·
                            Horario: <strong>${horaIni} a ${horaFin} hs</strong> ·
                            Modalidad: <strong>${t.modalidad.charAt(0).toUpperCase() + t.modalidad.slice(1)}</strong>
                        </p>
                        ${motivoHtml}
                        <span class="estado-tag estado-${t.estado.toLowerCase()}">
                            ${t.estado.toUpperCase()}
                        </span>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        ${btnJitsi}
                        ${btnCancelar}
                    </div>
                </div>
            `;
        }).join('');
    }

    function mostrarDetalleTurnoAbogado(t) {
        const panel = document.getElementById('detalle-turno-seleccionado');
        const inicio = new Date(t.inicio);
        const fin = new Date(t.fin);
        const fechaStr = inicio.toLocaleDateString('es-AR');
        const horaIni = inicio.toTimeString().slice(0, 5);
        const horaFin = fin.toTimeString().slice(0, 5);
        const clienteNombre = t.cliente ? t.cliente.name : 'Cliente';
        const esReservado = t.estado === 'reservado';
        const esFuturo = inicio > new Date();

        const btnJitsi = (t.modalidad === 'virtual' && esReservado)
            ? `<a href="/turnos/${t.id}/videollamada" class="btn-detalles">Entrar a videollamada</a>`
            : '';

        const btnCancelar = (esReservado && esFuturo)
            ? `<button type="button" class="btn-cancelar-turno" onclick="cancelarTurnoAbogado(${t.id})">Cancelar turno</button>`
            : '';

        panel.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px; flex-wrap: wrap;">
                <div>
                    <h3 style="color: #1a3b47; font-size: 16px; margin-bottom: 4px;">
                        Turno con ${escapeHtml(clienteNombre)} · ${fechaStr} (${horaIni} - ${horaFin} hs)
                    </h3>
                    <p style="font-size: 14px; color: #444;">
                        Modalidad: <strong>${t.modalidad}</strong> · Estado: <strong>${t.estado}</strong>
                        ${t.motivo ? `· Motivo: ${escapeHtml(t.motivo)}` : ''}
                    </p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    ${btnJitsi}
                    ${btnCancelar}
                    <button type="button" class="btn-logout" style="color: #1a3b47; border-color: #ccc;" onclick="document.getElementById('detalle-turno-seleccionado').style.display='none'">Cerrar</button>
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