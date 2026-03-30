@extends('layouts.app')

@section('title', 'Formulario de Encuesta')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-clipboard2-heart me-2"></i>Formulario de Encuesta</h5>
            </div>
            <div class="card-body p-4">

                {{-- Selector de Encuesta --}}
                <div class="mb-4">
                    <label for="selectEncuesta" class="form-label fw-semibold fs-5">
                        Seleccione el tipo de atención a evaluar:
                    </label>
                    <select id="selectEncuesta" class="form-select form-select-lg">
                        <option value="">-- Seleccione una encuesta --</option>
                        @foreach($encuestas as $encuesta)
                            <option value="{{ $encuesta->codigo_encuesta }}">
                                {{ $encuesta->nombre_encuesta }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Spinner de carga --}}
                <div id="loadingSpinner" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2">Cargando preguntas...</p>
                </div>

                {{-- Formulario de preguntas (se llena dinámicamente) --}}
                <form id="formEncuesta" action="/guardar" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="codigo_encuesta" id="codigoEncuestaInput">

                    <div id="preguntasContainer"></div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-send-check me-2"></i>Enviar Encuesta
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectEncuesta    = document.getElementById('selectEncuesta');
    const loadingSpinner    = document.getElementById('loadingSpinner');
    const formEncuesta      = document.getElementById('formEncuesta');
    const preguntasContainer = document.getElementById('preguntasContainer');
    const codigoInput       = document.getElementById('codigoEncuestaInput');
    const csrfToken         = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const LABELS = ['Muy malo', 'Malo', 'Regular', 'Bueno', 'Excelente'];
    const COLORS = ['danger', 'warning', 'secondary', 'info', 'success'];

    selectEncuesta.addEventListener('change', async function () {
        const codigo = this.value;

        formEncuesta.classList.add('d-none');
        preguntasContainer.innerHTML = '';

        if (!codigo) return;

        loadingSpinner.classList.remove('d-none');

        try {
            const response = await fetch(`/preguntas/${codigo}`, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) throw new Error('Error al cargar preguntas');

            const data = await response.json();

            codigoInput.value = codigo;

            let html = `<h6 class="text-muted mb-3">Encuesta: <strong class="text-dark">${data.encuesta}</strong></h6>`;
            html += `<p class="text-muted small mb-3">Califique del 1 (Muy malo) al 5 (Excelente)</p>`;

            data.preguntas.forEach((pregunta, idx) => {
                html += `
                <div class="card mb-3 pregunta-card">
                    <div class="card-body">
                        <p class="fw-semibold mb-3">
                            <span class="badge bg-primary me-2">${idx + 1}</span>
                            ${pregunta.descripcion}
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            ${[1,2,3,4,5].map(val => `
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="respuestas[${pregunta.num_pregunta}]"
                                        id="p${pregunta.num_pregunta}_v${val}"
                                        value="${val}"
                                        required
                                    >
                                    <label
                                        class="form-check-label rating-label badge bg-${COLORS[val-1]} text-white fs-6 px-3 py-2"
                                        for="p${pregunta.num_pregunta}_v${val}"
                                        title="${LABELS[val-1]}"
                                    >
                                        ${val} — ${LABELS[val-1]}
                                    </label>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>`;
            });

            preguntasContainer.innerHTML = html;
            formEncuesta.classList.remove('d-none');

        } catch (error) {
            preguntasContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error al cargar las preguntas. Por favor intente nuevamente.
                </div>`;
            formEncuesta.classList.remove('d-none');
        } finally {
            loadingSpinner.classList.add('d-none');
        }
    });

    // Si hay errores de validación, restaurar el formulario con el mismo select
    @if($errors->any() && old('codigo_encuesta'))
        selectEncuesta.value = '{{ old('codigo_encuesta') }}';
        selectEncuesta.dispatchEvent(new Event('change'));
    @endif
});
</script>
@endpush
