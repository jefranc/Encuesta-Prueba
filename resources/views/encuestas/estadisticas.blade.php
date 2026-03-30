@extends('layouts.app')

@section('title', 'Estadísticas de Encuestas')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Estadísticas Generales de Encuestas</h5>
                <a href="/" class="btn btn-light btn-sm">
                    <i class="bi bi-pencil-square me-1"></i>Nueva Encuesta
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width:60px">ID</th>
                                <th>Descripción de Encuesta</th>
                                <th class="text-center">N° Preguntas</th>
                                <th class="text-center">Encuestados</th>
                                <th class="text-center">Puntaje Máx.</th>
                                <th class="text-center">Promedio Obtenido</th>
                                <th class="text-center">% Cumplimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estadisticas as $stat)
                                @php
                                    $pct = $stat['porcentaje'];
                                    $color = match(true) {
                                        $pct >= 80 => 'success',
                                        $pct >= 60 => 'info',
                                        $pct >= 40 => 'warning',
                                        default    => 'danger',
                                    };
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold">{{ $stat['codigo'] }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $stat['nombre'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $stat['n_preguntas'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($stat['total_encuestados'] > 0)
                                            <span class="badge bg-primary fs-6">{{ $stat['total_encuestados'] }}</span>
                                        @else
                                            <span class="text-muted">Sin datos</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="text-muted">{{ $stat['max_posible'] }} pts</span>
                                    </td>
                                    <td class="text-center">
                                        @if($stat['total_encuestados'] > 0)
                                            <strong>{{ number_format($stat['promedio'], 2) }} pts</strong>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($stat['total_encuestados'] > 0)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:18px; min-width:80px;">
                                                    <div
                                                        class="progress-bar bg-{{ $color }}"
                                                        role="progressbar"
                                                        style="width: {{ $pct }}%"
                                                        aria-valuenow="{{ $pct }}"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <span class="badge bg-{{ $color }} badge-porcentaje">{{ $pct }}%</span>
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        No hay encuestas disponibles.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
