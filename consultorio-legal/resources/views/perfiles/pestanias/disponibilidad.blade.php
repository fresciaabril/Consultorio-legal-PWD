<div id="tab-disponibilidad" class="tab-content">
    <div class="card">
        <h2>Horarios habituales de atención</h2>
        <p style="color: #555; font-size: 14px; margin-bottom: 20px;">
            Definí los días de la semana y las franjas horarias en las que los clientes podrán reservar turnos de 2 horas en tu agenda.
        </p>

        <form id="form-disponibilidad">
            <div class="form-grid">
                <div class="form-group">
                    <label for="dia_semana">Día de la semana</label>
                    <select name="dia_semana" id="dia_semana" required>
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                        <option value="7">Domingo</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="disp_hora_inicio">Hora de inicio</label>
                    <input type="time" name="hora_inicio" id="disp_hora_inicio" value="09:00" required>
                </div>

                <div class="form-group">
                    <label for="disp_hora_fin">Hora de fin</label>
                    <input type="time" name="hora_fin" id="disp_hora_fin" value="17:00" required>
                </div>
            </div>

            <button type="submit" class="btn-guardar">Guardar disponibilidad</button>
        </form>

        <div class="lista-config">
            <h3 style="font-size: 16px; color: #1a3b47; margin-bottom: 10px;">Franjas activas actuales:</h3>
            <div id="lista-disponibilidades">
                <p style="color: #777777; font-size: 14px;">Cargando horarios configurados...</p>
            </div>
        </div>
    </div>
</div>

<script>
    const NOMBRES_DIAS = {
        1: 'Lunes',
        2: 'Martes',
        3: 'Miércoles',
        4: 'Jueves',
        5: 'Viernes',
        6: 'Sábado',
        7: 'Domingo'
    };

    document.addEventListener('DOMContentLoaded', function() {
        cargarDisponibilidades();
        const formDisp = document.getElementById('form-disponibilidad');
        if (!formDisp) return;

        formDisp.addEventListener('submit', async function(e) {
            e.preventDefault();

            const payload = {
                dia_semana: parseInt(document.getElementById('dia_semana').value, 10),
                hora_inicio: document.getElementById('disp_hora_inicio').value,
                hora_fin: document.getElementById('disp_hora_fin').value
            };

            try {
                const res = await fetch('/abogado/disponibilidades', {
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
                        : (data.mensaje || 'No se pudo guardar la disponibilidad.');
                    mostrarAlertaAbogado(primerError, 'error');
                    return;
                }

                mostrarAlertaAbogado(data.mensaje || 'Disponibilidad guardada correctamente.', 'exito');
                await cargarDisponibilidades();
                if (calendarioAbogado) {
                    calendarioAbogado.refetchEvents();
                }
            } catch (err) {
                mostrarAlertaAbogado('Error de conexión al guardar disponibilidad.', 'error');
            }
        });
    });

    async function cargarDisponibilidades() {
        const contenedor = document.getElementById('lista-disponibilidades');
        if (!contenedor) return;

        try {
            const res = await fetch('/abogado/disponibilidades', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;

            const lista = await res.json();
            if (!lista.length) {
                contenedor.innerHTML = '<p style="color: #777777; font-size: 14px;">No tenés horarios de atención configurados todavía.</p>';
                return;
            }

            contenedor.innerHTML = lista.map(d => `
                <div class="item-config">
                    <span>
                        <strong>${NOMBRES_DIAS[d.dia_semana] || 'Día ' + d.dia_semana}:</strong>
                        ${d.hora_inicio.slice(0, 5)} hs a ${d.hora_fin.slice(0, 5)} hs
                    </span>
                    <button type="button" class="btn-cancelar-turno" style="padding: 5px 12px; font-size: 13px;" onclick="eliminarDisponibilidad(${d.id})">
                        Eliminar
                    </button>
                </div>
            `).join('');
        } catch (e) {
            console.error('Error cargando disponibilidades:', e);
        }
    }

    async function eliminarDisponibilidad(id) {
        if (!confirm('¿Eliminar esta franja de atención?')) return;

        try {
            const res = await fetch(`/abogado/disponibilidades/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                }
            });
            const data = await res.json();

            if (!res.ok) {
                mostrarAlertaAbogado(data.mensaje || 'No se pudo eliminar.', 'error');
                return;
            }

            mostrarAlertaAbogado(data.mensaje || 'Disponibilidad eliminada.', 'exito');
            await cargarDisponibilidades();
            if (calendarioAbogado) {
                calendarioAbogado.refetchEvents();
            }
        } catch (e) {
            mostrarAlertaAbogado('Error al eliminar disponibilidad.', 'error');
        }
    }
</script>