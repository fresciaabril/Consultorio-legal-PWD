<?php
 namespace App\Services;

 use App\Models\Disponibilidad;
 use App\Models\BloqueoAgenda;
 use Carbon\Carbon;

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

    public function obtenerHorariosLibres($abogadoId, $fecha, $duracion = 30)
    {
        if ($duracion <= 0) {
            throw new \InvalidArgumentException(
                'La duración del turno debe ser mayor a cero.'
            );
        }


        $disponibilidades = $this->obtenerDisponibilidad($abogadoId, $fecha);
        $bloqueos = $this->obtenerBloqueos($abogadoId, $fecha);

        $horariosLibres = [];

        foreach ($bloqueos as $bloqueo) {
            if ($bloqueo->hora_inicio === null && $bloqueo->hora_fin === null) {
                return [];
            }
        }

        foreach ($disponibilidades as $disponibilidad) {
            $inicio = Carbon::parse($fecha . ' ' . $disponibilidad->hora_inicio);
            $fin = Carbon::parse($fecha . ' ' . $disponibilidad->hora_fin);

            while ($inicio->copy()->addMinutes($duracion)->lte($fin)) {
                $finTurno = $inicio->copy()->addMinutes($duracion);
                $ocupado = false;

                foreach ($bloqueos as $bloqueo) {
                    $inicioBloqueo = Carbon::parse($fecha . ' ' . $bloqueo->hora_inicio);
                    $finBloqueo = Carbon::parse($fecha . ' ' . $bloqueo->hora_fin);

                    if ($inicio->lt($finBloqueo) && $finTurno->gt($inicioBloqueo)) {
                        $ocupado = true;
                        break;
                    }
                }

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