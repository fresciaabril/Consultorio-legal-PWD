<div id="tab-consultas" class="tab-content active">
    <div class="card">
        <h2>Consultas y turnos asignados</h2>

        @forelse ($solicitudes as $solicitud)
            <div class="solicitud-item">
                <div class="solicitud-info">
                    <h3>{{ $solicitud->nombre }}</h3>
                    <p style="margin-bottom: 8px;">
                        <strong>Área:</strong> {{ ucfirst($solicitud->area) }}
                    </p>
                    <span class="estado-tag estado-{{ strtolower($solicitud->estado) }}">
                        {{ $solicitud->estado }}
                    </span>
                </div>

                <a href="/consultas/abogado/{{ $solicitud->id }}" class="btn-detalles">
                    Ver consulta
                </a>
            </div>
        @empty
            <p style="color: #777;">No hay consultas asignadas por el momento.</p>
        @endforelse
    </div>
</div>