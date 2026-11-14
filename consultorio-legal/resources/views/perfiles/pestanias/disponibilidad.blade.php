<div id="tab-disponibilidad" class="tab-content">
    <div class="card">
        <h2>Horarios habituales de atención</h2>
        <p style="color: #555; font-size: 14px; margin-bottom: 20px;">
            Definí los días de la semana y las franjas horarias en las que los clientes podrán reservar turnos en tu agenda.
        </p>

        <form action="/abogado/disponibilidades" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="dia_semana">Día de la semana</label>
                    <select name="dia_semana" id="dia_semana" required>
                        <option value="lunes">Lunes</option>
                        <option value="martes">Martes</option>
                        <option value="miercoles">Miércoles</option>
                        <option value="jueves">Jueves</option>
                        <option value="viernes">Viernes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hora_inicio">Hora de inicio</label>
                    <input type="time" name="hora_inicio" id="hora_inicio" value="09:00" required>
                </div>

                <div class="form-group">
                    <label for="hora_fin">Hora de fin</label>
                    <input type="time" name="hora_fin" id="hora_fin" value="17:00" required>
                </div>

                <div class="form-group">
                    <label for="duracion">Duración del turno</label>
                    <select name="duracion" id="duracion">
                        <option value="30">30 minutos</option>
                        <option value="45">45 minutos</option>
                        <option value="60" selected>1 hora</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn-guardar">Guardar disponibilidad</button>
        </form>

        <div class="lista-config">
            <h3 style="font-size: 16px; color: #1a3b47; margin-bottom: 10px;">Franjas activas actuales:</h3>
            <div class="item-config">
                <span><strong>Lunes y Miércoles:</strong> 09:00 hs a 13:00 hs (Turnos de 1 hora)</span>
                <span style="color: #137333; font-weight: 600;">Activo</span>
            </div>
        </div>
    </div>
</div>