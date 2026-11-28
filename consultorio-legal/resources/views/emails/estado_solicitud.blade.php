<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultorio Legal</title>
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif; padding: 25px;">

    <div style="max-width: 600px; margin: auto; background-color: white; border-radius: 10px; overflow: hidden;">

        <div style="background-color: #244438; color: white; padding: 25px; text-align: center;">
            <h1>Consultorio Legal</h1>
        </div>

        <div style="padding: 30px;">

            <h2>Hola, {{ $solicitud->nombre }}</h2>

            @if ($solicitud->estado === 'pendiente')

                <h3 style="color: #b8860b;">Solicitud recibida</h3>

                <p>Hemos recibido tu solicitud correctamente.</p>

                <p>El abogado revisará tu consulta y te notificaremos por correo cuando haya una respuesta.</p>

            @elseif ($solicitud->estado === 'aceptada')

                <h3 style="color: #27834a;">Solicitud aceptada</h3>

                <p>Nos complace informarte que tu solicitud ha sido aceptada.</p>

                <p>Ya podés continuar con la creación de tu cuenta, reservar turnos y acceder a nuestras consultas.</p>

            @elseif ($solicitud->estado === 'rechazada')

                <h3 style="color: #b83232;">Solicitud rechazada</h3>

                <p>Lamentamos informarte que tu solicitud no ha sido aceptada.</p>

                <p>Gracias por confiar en nuestro consultorio.</p>

            @endif

            <p>Podés consultar el estado de tu solicitud desde el siguiente enlace:</p>

            <div style="text-align: center; margin: 30px 0;">

                <a href="{{ $enlace }}"
                   style="background-color: #244438; color: white; padding: 15px 25px; text-decoration: none; border-radius: 6px; display: inline-block;">

                    @if ($solicitud->estado === 'aceptada')
                        Continuar con mi registro
                    @else
                        Consultar mi solicitud
                    @endif

                </a>

            </div>

            <p>Por seguridad, no compartas este enlace con otras personas.</p>

            <p>Saludos,<br>Consultorio Legal.</p>

        </div>

        <div style="background-color: #eeeeee; padding: 15px; text-align: center; color: #666666; font-size: 12px;">
            Este correo fue generado automáticamente.
        </div>

    </div>

</body>
</html>