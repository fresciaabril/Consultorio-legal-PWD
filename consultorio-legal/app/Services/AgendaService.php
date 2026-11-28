<?php
    namespace App\Services;

    use App\Models\Disponibilidad;
    use App\Models\BloqueoAgenda;
    use Carbon\Carbon;
    use App\Models\Turno;

    class AgendaService{
        public function obtenerDisponibilidad($abogadoid, $fecha){
            $diaSemana = Carbon::parse($fecha)->dayOfWeekIso;

            $disponibilidades = Disponibilidad::where('user_id', $abogadoid)
            ->where('dia_semana', $diaSemana)
            ->orderBy('hora_inicio')
            ->get();

            return $disponibilidades;
        }

        public function obtenerBloqueos($abogadoId, $fecha)
        {
            $bloqueos = BloqueoAgenda::where('user_id', $abogadoId)
                ->whereDate('fecha', $fecha)
                ->orderBy('hora_inicio')
                ->get();

            return $bloqueos;
        }

        
        public function obtenerHorariosLibres($abogadoId, $fecha, $duracion = 120)
        {
            if ($duracion <= 0) {
                throw new \InvalidArgumentException(
                    'La duración del turno debe ser mayor a cero.'
                );
            }

            // 1. Consultamos disponibilidad y bloqueos
            $disponibilidades = $this->obtenerDisponibilidad($abogadoId, $fecha);
            $bloqueos = $this->obtenerBloqueos($abogadoId, $fecha);

            // 2. Consultamos turnos reservados para esa fecha
            $inicioDia = Carbon::parse($fecha)->startOfDay();
            $finDia = $inicioDia->copy()->addDay();

            $turnosReservados = Turno::where('abogado_id', $abogadoId)
                ->where('estado', 'reservado')
                ->where('inicio', '<', $finDia)
                ->where('fin', '>', $inicioDia)
                ->get();

            $horariosLibres = [];

            // 3. Si está bloqueado el día completo, no hay horarios
            foreach ($bloqueos as $bloqueo) {
                if ($bloqueo->hora_inicio === null &&
                    $bloqueo->hora_fin === null) {
                    return [];
                }
            }

            // 4. Recorremos los horarios de atención
            foreach ($disponibilidades as $disponibilidad) {
                $inicio = Carbon::parse(
                    $fecha . ' ' . $disponibilidad->hora_inicio
                );

                $fin = Carbon::parse(
                    $fecha . ' ' . $disponibilidad->hora_fin
                );

                while ($inicio->copy()->addMinutes($duracion)->lte($fin)) {
                    $finTurno = $inicio->copy()->addMinutes($duracion);
                    $ocupado = false;

                    // 5. Comprobamos bloqueos parciales
                    foreach ($bloqueos as $bloqueo) {
                        if ($bloqueo->hora_inicio === null ||
                            $bloqueo->hora_fin === null) {
                            continue;
                        }

                        $inicioBloqueo = Carbon::parse(
                            $fecha . ' ' . $bloqueo->hora_inicio
                        );

                        $finBloqueo = Carbon::parse(
                            $fecha . ' ' . $bloqueo->hora_fin
                        );

                        if ($inicio->lt($finBloqueo) &&
                            $finTurno->gt($inicioBloqueo)) {
                            $ocupado = true;
                            break;
                        }
                    }

                    // 6. Comprobamos reservas existentes
                    if (!$ocupado) {
                        foreach ($turnosReservados as $turno) {
                            if ($inicio->lt($turno->fin) &&
                                $finTurno->gt($turno->inicio)) {
                                $ocupado = true;
                                break;
                            }
                        }
                    }

                    // 7. Agregamos únicamente horarios futuros y libres
                    if (!$ocupado && $inicio->gt(Carbon::now())) {
                        $horariosLibres[] = [
                            'hora_inicio' => $inicio->format('H:i'),
                            'hora_fin' => $finTurno->format('H:i'),
                        ];
                    }

                    $inicio->addMinutes($duracion);
                }
            }

            return $horariosLibres;
        }

    }
?>