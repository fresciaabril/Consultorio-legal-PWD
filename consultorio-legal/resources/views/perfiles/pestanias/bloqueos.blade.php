<div id="tab-bloqueos" class="tab-content">
    <div class="card">
        <h2>Bloquear días u horarios no disponibles</h2>
        <p style="color: #555; font-size: 14px; margin-bottom: 20px;">
            Seleccioná fechas completas (dejando las horas vacías) o un rango horario específico para que no aparezcan libres en el calendario.
        </p>

        <form id="form-bloqueos">
            <div class="form-grid">
                <div class="form-group">
                    <label for="fecha_bloqueo">Fecha a bloquear</label>
                    <input type="date" name="fecha" id="fecha_bloqueo" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="bloq_hora_inicio">Hora inicio (opcional)</label>
                    <input type="time" name="hora_inicio" id="bloq_hora_inicio">
                </div>

                <div class="form-group">
                    <label for="bloq_hora_fin">Hora fin (opcional)</label>
                    <input type="time" name="hora_fin" id="bloq_hora_fin">
                </div>

                <div class="form-group">
                    <label for="motivo_bloqueo">Motivo (opcional)</label>
                    <input type="text" name="motivo" id="motivo_bloqueo" placeholder="Ej: Audiencia, Feriado...">
                </div>
            </div>

            <button type="submit" class="btn-guardar btn-bloquear">Guardar bloqueo</button>
        </form>

        <div class="lista-config">
            <h3 style="font-size: 16px; color: #1a3b47; margin-bottom: 10px;">Fechas y horarios bloqueados:</h3>
            <div id="lista-bloqueos">
                <p style="color: #777777; font-size: 14px;">Cargando bloqueos...</p>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formBloq = document.getElementById('form-bloqueos');
        if (!formBloq) return;

        formBloq.addEventListener('submit', async function(e) {
            e.preventDefault();

            const payload = {
                fecha: document.getElementById('fecha_bloqueo').value,
                hora_inicio: document.getElementById('bloq_hora_inicio').value || null,
                hora_fin: document.getElementById('bloq_hora_fin').value || null,
                motivo: document.getElementById('motivo_bloqueo').value.trim() || null
            };

            try {
                const res = await fetch('/abogado/bloqueos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (!res.ok) {
                    const primerError = data.errors
                        ? Object.values(data.errors).flat()[0]
                        : (data.mensaje || 'No se pudo guardar el bloqueo.');
                    mostrarAlertaAbogado(primerError, 'error');
                    return;
                }

                mostrarAlertaAbogado(data.mensaje || 'Bloqueo guardado correctamente.', 'exito');
                formBloq.reset();
                await cargarBloqueos();
                if (calendarioAbogado) {
                    calendarioAbogado.refetchEvents();
                }
            } catch (err) {
                mostrarAlertaAbogado('Error de conexión al guardar el bloqueo.', 'error');
            }
        });
    });

    async function cargarBloqueos() {
        const contenedor = document.getElementById('lista-bloqueos');
        if (!contenedor) return;

        try {
            const res = await fetch('/abogado/bloqueos', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;

            const lista = await res.json();
            if (!lista.length) {
                contenedor.innerHTML = '<p style="color: #777777; font-size: 14px;">No hay fechas ni horarios bloqueados.</p>';
                return;
            }

            contenedor.innerHTML = lista.map(b => {
                const fechaSolo = b.fecha.split('T')[0];
                const rango = (b.hora_inicio && b.hora_fin)
                    ? `de ${b.hora_inicio.slice(0, 5)} a ${b.hora_fin.slice(0, 5)} hs`
                    : 'Día completo';
                const motivo = b.motivo ? ` — ${escapeHtml(b.motivo)}` : '';

                return `
                    <div class="item-config">
                        <span>
                            <strong>${fechaSolo}</strong> (${rango})${motivo}
                        </span>
                        <button type="button" class="btn-cancelar-turno" style="padding: 5px 12px; font-size: 13px;" onclick="eliminarBloqueo(${b.id})">
                            Quitar bloqueo
                        </button>
                    </div>
                `;
            }).join('');
        } catch (e) {
            console.error('Error cargando bloqueos:', e);
        }
    }

    async function eliminarBloqueo(id) {
        if (!confirm('¿Quitar este bloqueo de la agenda?')) return;

        try {
            const res = await fetch(`/abogado/bloqueos/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });
            const data = await res.json();

            if (!res.ok) {
                mostrarAlertaAbogado(data.mensaje || 'No se pudo eliminar el bloqueo.', 'error');
                return;
            }

            mostrarAlertaAbogado(data.mensaje || 'Bloqueo eliminado.', 'exito');
            await cargarBloqueos();
            if (calendarioAbogado) {
                calendarioAbogado.refetchEvents();
            }
        } catch (e) {
            mostrarAlertaAbogado('Error al eliminar bloqueo.', 'error');
        }
    }
</script>