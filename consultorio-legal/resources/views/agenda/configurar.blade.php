<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de agenda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
    * {
        box-sizing: border-box;
        font-family: system-ui, sans-serif;
    }

    body {
        background-color: #f4f3ef;
        color: #2c3e50;
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h1, h2 {
        color: #1a3b47;
    }

    form {
        background-color: white;
        padding: 25px;
        border-radius: 6px;
        margin-bottom: 25px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
    }

    label {
        font-weight: 500;
    }

    input, select {
        padding: 10px;
        border: 1px solid #cccccc;
        border-radius: 4px;
    }

    button {
        background-color: #1a3b47;
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
    }

    button:hover {
        background-color: #122a33;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        margin-bottom: 25px;
    }

    th, td {
        padding: 12px;
        border: 1px solid #eeeeee;
        text-align: left;
    }

    th {
        background-color: #1a3b47;
        color: white;
    }

    td button {
        background-color: #c5221f;
    }

    td button:hover {
        background-color: #9e1b19;
    }

    hr {
        border: none;
        border-top: 1px solid #dddddd;
        margin: 30px 0;
    }

    #mensaje {
        font-weight: 600;
    }

    .btn-volver {
    display: inline-block;
    background-color: #1a3b47;
    color: white;
    text-decoration: none;
    padding: 10px 18px;
    border-radius: 4px;
    font-weight: 600;
    margin-bottom: 15px;
}

.btn-volver:hover {
    background-color: #122a33;
}
</style>
</head>
<body>
    <a href="{{ url('/abogado') }}" class="btn-volver">
        ← Volver a mi perfil
    </a>

    <h1>Configuración de mi agenda</h1>
    <p id="mensaje"></p>

    <h2>Agregar disponibilidad</h2>

    <form id="formDisponibilidad" action="{{ route('abogado.disponibilidades.store') }}" method="POST">

        @csrf

        <label for="dia_semana">Día de la semana:</label>

        <select name="dia_semana" id="dia_semana" required>
            <option value="1">Lunes</option>
            <option value="2">Martes</option>
            <option value="3">Miércoles</option>
            <option value="4">Jueves</option>
            <option value="5">Viernes</option>
            <option value="6">Sábado</option>
            <option value="7">Domingo</option>
        </select>

        <br><br>

        <label for="hora_inicio">Hora de inicio:</label>
        <input type="time" name="hora_inicio"
                id="hora_inicio" required>

        <br><br>

        <label for="hora_fin">Hora de finalización:</label>
        <input type="time" name="hora_fin"
                id="hora_fin" required>

        <br><br>

        <button type="submit">Guardar disponibilidad</button>

    </form>

    <hr>

    <h2>Bloquear fecha u horario</h2>

    <hr>

    <h2>Mis horarios configurados</h2>

    <table border="1">
        <thead>
            <tr>
                <th>Día</th>
                <th>Hora de inicio</th>
                <th>Hora de finalización</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody id="listaDisponibilidades">
        </tbody>
    </table>

    <form id="formBloqueo" action="{{ route('abogado.bloqueos.store') }}" method="POST">

    @csrf

    <label for="fecha">Fecha:</label>
    <input type="date" name="fecha" id="fecha" required>

    <br><br>

    <label for="bloqueo_inicio">Hora de inicio:</label>
    <input type="time" name="hora_inicio" id="bloqueo_inicio">

    <br><br>

    <label for="bloqueo_fin">Hora de finalización:</label>
    <input type="time" name="hora_fin" id="bloqueo_fin">

    <br><br>

    <label for="motivo">Motivo:</label>
    <input type="text" name="motivo" id="motivo" maxlength="255">

    <br><br>

    <p>Para bloquear el día completo, dejá ambas horas vacías.</p>

    <button type="submit">Guardar bloqueo</button>

</form>

<hr>

<h2>Mis bloqueos registrados</h2>

<table border="1">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Hora de inicio</th>
            <th>Hora de finalización</th>
            <th>Motivo</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody id="listaBloqueos">
    </tbody>
</table>

<script>
    const mensaje = document.getElementById('mensaje');
    async function cargarDisponibilidades() {
        const dias = [
            '', 'Lunes', 'Martes', 'Miércoles',
            'Jueves', 'Viernes', 'Sábado', 'Domingo'
        ];

        try {
            const respuesta = await fetch(
                "{{ route('abogado.disponibilidades') }}",
                {
                    //le indicamos al servidor que queremos una respuesta json
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            if (!respuesta.ok) {
                throw new Error('Error al consultar horarios');
            }

            const disponibilidades = await respuesta.json();

            const tabla = document.getElementById(
                'listaDisponibilidades'
            );

            tabla.innerHTML = '';

            disponibilidades.forEach(disponibilidad => {
                const fila = tabla.insertRow();

                fila.insertCell().textContent =
                    dias[disponibilidad.dia_semana];

                fila.insertCell().textContent =
                    disponibilidad.hora_inicio;

                fila.insertCell().textContent =
                    disponibilidad.hora_fin;

                const celdaAcciones = fila.insertCell();

                const boton = document.createElement('button');

                boton.textContent = 'Eliminar';
                boton.type = 'button';

                boton.addEventListener('click', () => {
                    eliminarDisponibilidad(disponibilidad.id);
                });

                celdaAcciones.appendChild(boton);
            });

        } catch (error) {
            mensaje.textContent = 'No se pudieron cargar los horarios.';
            mensaje.style.color = 'red';
        }
    }

    async function cargarBloqueos() {
        try {
            const respuesta = await fetch(
                "{{ route('abogado.bloqueos') }}",
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            if (!respuesta.ok) {
                throw new Error('Error al consultar bloqueos');
            }

            const bloqueos = await respuesta.json();

            const tabla = document.getElementById('listaBloqueos');

            tabla.innerHTML = '';

            bloqueos.forEach(bloqueo => {
                const fila = tabla.insertRow();

                fila.insertCell().textContent =
                    bloqueo.fecha.substring(0, 10);

                fila.insertCell().textContent =
                    bloqueo.hora_inicio || 'Día completo';

                fila.insertCell().textContent =
                    bloqueo.hora_fin || '-';

                fila.insertCell().textContent =
                    bloqueo.motivo || 'Sin motivo';

                const celdaAcciones = fila.insertCell();

                const boton = document.createElement('button');

                boton.textContent = 'Eliminar';
                boton.type = 'button';

                boton.addEventListener('click', () => {
                    eliminarBloqueo(bloqueo.id);
                });

                celdaAcciones.appendChild(boton);
            });

        } catch (error) {
            mensaje.textContent = 'No se pudieron cargar los bloqueos.';
            mensaje.style.color = 'red';
        }
    }

    async function consultarHorariosLibres(event) {
        event.preventDefault();

        const fecha = document.getElementById('fecha_consulta').value;
        const lista = document.getElementById('listaHorariosLibres');

        lista.innerHTML = '';

        try {
            const respuesta = await fetch(
                "{{ route('abogado.horarios-libres') }}?fecha=" + encodeURIComponent(fecha),
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            const resultado = await respuesta.json();

            if (!respuesta.ok) {
                throw new Error(resultado.message || 'Error al consultar horarios');
            }

            if (resultado.horarios_libres.length === 0) {
                lista.textContent = 'No hay horarios disponibles.';
                return;
            }

            resultado.horarios_libres.forEach(horario => {
                const elemento = document.createElement('li');

                elemento.textContent =
                    horario.hora_inicio + ' - ' + horario.hora_fin;

                lista.appendChild(elemento);
            });

        } catch (error) {
            lista.textContent = error.message;
        }
    }

    async function eliminarDisponibilidad(id) {
        if (!confirm('¿Deseás eliminar este horario?')) {
            return;
        }

        try {
            const respuesta = await fetch(
                "{{ url('/abogado/disponibilidades') }}/" + id,
                {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                }
            );

            const resultado = await respuesta.json();

            if (!respuesta.ok) {
                throw new Error(
                    resultado.message || 'No se pudo eliminar.'
                );
            }

            mensaje.textContent = resultado.mensaje;
            mensaje.style.color = 'green';

            await cargarDisponibilidades();

        } catch (error) {
            mensaje.textContent = error.message;
            mensaje.style.color = 'red';
        }
    }

    async function eliminarBloqueo(id) {
        if (!confirm('¿Deseás eliminar este bloqueo?')) {
            return;
        }

        try {
            const respuesta = await fetch(
                "{{ url('/abogado/bloqueos') }}/" + id,
                {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                }
            );

            const resultado = await respuesta.json();

            if (!respuesta.ok) {
                throw new Error(
                    resultado.message || 'No se pudo eliminar.'
                );
            }

            mensaje.textContent = resultado.mensaje;
            mensaje.style.color = 'green';

            await cargarBloqueos();

        } catch (error) {
            mensaje.textContent = error.message;
            mensaje.style.color = 'red';
        }
    }

    async function guardarFormulario(event) {
        event.preventDefault();

        const formulario = event.target;
        const datos = new FormData(formulario);

        try {
            const respuesta = await fetch(formulario.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: datos
            });

            const resultado = await respuesta.json();

            if (respuesta.ok) {
                mensaje.textContent = resultado.mensaje;
                mensaje.style.color = 'green';

                formulario.reset();
                if (formulario.id === 'formDisponibilidad') {
                            await cargarDisponibilidades();
                        }

                        if (formulario.id === 'formBloqueo') {
                            await cargarBloqueos();
                        }
            } else {
                mensaje.textContent = resultado.mensaje
                    || resultado.message
                    || 'No se pudieron guardar los datos.';

                mensaje.style.color = 'red';
            }

        } catch (error) {
            mensaje.textContent = 'Ocurrió un error de conexión.';
            mensaje.style.color = 'red';
        }
    }

    document.getElementById('formDisponibilidad')
        .addEventListener('submit', guardarFormulario);

    document.getElementById('formBloqueo')
        .addEventListener('submit', guardarFormulario);

    document.getElementById('formHorariosLibres')
    .addEventListener('submit', consultarHorariosLibres);

    // Activar después de ejecutar las migraciones.
// cargarDisponibilidades();
// cargarBloqueos();
</script>

<hr>

<h2>Consultar horarios libres</h2>

<form id="formHorariosLibres">

    <label for="fecha_consulta">Seleccionar fecha:</label>

    <input type="date" id="fecha_consulta" required>

    <button type="submit">Consultar horarios</button>

</form>

<ul id="listaHorariosLibres"></ul>
</body>
</html>