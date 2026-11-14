<div id="tab-bloqueos" class="tab-content">
    <div class="card">
        <h2>Bloquear días u horarios no disponibles</h2>
        <p style="color: #555; font-size: 14px; margin-bottom: 20px;">
            Seleccioná fechas específicas (feriados, audiencias externas o vacaciones) para que no aparezcan libres en el calendario.
        </p>

        <form action="/abogado/bloqueos" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="fecha_bloqueo">Fecha a bloquear</label>
                    <input type="date" name="fecha" id="fecha_bloqueo" required>
                </div>

                <div class="form-group">
                    <label for="motivo_bloqueo">Motivo (opcional)</label>
                    <input type="text" name="motivo" id="motivo_bloqueo" placeholder="Ej: Audiencia en tribunales, Feriado...">
                </div>
            </div>

            <button type="submit" class="btn-guardar btn-bloquear">Bloquear fecha</button>
        </form>

        <div class="lista-config">
            <h3 style="font-size: 16px; color: #1a3b47; margin-bottom: 10px;">Próximas fechas bloqueadas:</h3>
            <div class="item-config">
                <span><strong>20/11/2026</strong> — Audiencia presencial en juzgado</span>
                <span style="color: #c5221f; font-weight: 600;">Bloqueado</span>
            </div>
        </div>
    </div>
</div>