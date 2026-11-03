@extends('citizen.layouts.app')

@section('title', 'Seguimiento de Folio | Álamo Reporta')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('css/citizen/track.css') }}?v={{ time() }}">
@endsection

@section('content')
    <div id="track-config"
         data-search-base="{{ url('/reportar/api/folio') }}"
         data-initial-folio="{{ $initialFolio }}"
         class="is-hidden">
    </div>

    <!-- BUSCADOR DE FOLIO OFICIAL -->
    <section class="search-card">
        <h2 class="search-title">Consulta el avance de tu reporte</h2>
        <p class="search-subtitle">Ingresa tu folio oficial (ejemplo: <strong>ALM-2026-0001</strong>) o toca uno de los recientes.</p>

        <form id="folio-search-form" class="search-bar-wrap" autocomplete="off">
            <input type="text" id="folio-input" class="search-input" placeholder="ALM-2026-0001" value="{{ $initialFolio }}" required>
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Buscar</span>
            </button>
        </form>

        @if(count($latestReports) > 0)
        <div class="quick-folios">
            <span class="quick-label">Folios recientes:</span>
            <div class="quick-chips">
                @foreach($latestReports as $item)
                    <button type="button" class="folio-chip" data-folio="{{ $item->folio }}">
                        {{ $item->folio }}
                    </button>
                @endforeach
            </div>
        </div>
        @endif
    </section>

    <p id="track-error" class="track-error-box is-hidden">No se encontró ningún reporte con ese número de folio.</p>

    <!-- RESULTADO DEL SEGUIMIENTO (LÍNEA DE TIEMPO Y EVIDENCIAS ANTES/DESPUÉS) -->
    <section id="track-result-card" class="result-card is-hidden">
        <div class="result-header">
            <span id="res-folio" class="result-folio">ALM-2026-0001</span>
            <span id="res-date" class="result-date"></span>
        </div>

        <h3 id="res-title" class="result-title"></h3>
        <p id="res-department" class="result-dept"></p>

        <!-- LÍNEA DE PROGRESO DE 3 ETAPAS -->
        <div class="progress-steps">
            <div id="step-recibido" class="p-step is-completed">
                <div class="p-circle"><i class="fa-solid fa-file-circle-check"></i></div>
                <span class="p-label">Recibido</span>
            </div>
            <div id="step-proceso" class="p-step">
                <div class="p-circle"><i class="fa-solid fa-person-digging"></i></div>
                <span class="p-label">En Proceso</span>
            </div>
            <div id="step-resuelto" class="p-step">
                <div class="p-circle"><i class="fa-solid fa-house-circle-check"></i></div>
                <span class="p-label">Resuelto</span>
            </div>
        </div>

        <!-- RESPUESTA OFICIAL DEL AYUNTAMIENTO -->
        <div class="municipality-note-box">
            <span class="note-heading"><i class="fa-solid fa-building-shield"></i> Dictamen del Municipio:</span>
            <p id="res-notes" class="note-content"></p>
        </div>

        <!-- COMPARATIVA VISUAL ANTES Y DESPUÉS -->
        <div class="comparison-grid">
            <div class="evidence-panel">
                <span class="panel-tag tag-before">1. Evidencia Ciudadana (Antes)</span>
                <img id="res-img-before" src="" alt="Foto Antes" class="panel-img">
            </div>

            <div class="evidence-panel">
                <span class="panel-tag tag-after">2. Evidencia Municipal (Después)</span>
                <img id="res-img-after" src="" alt="Foto Después" class="panel-img is-hidden">
                <div id="res-empty-after" class="empty-after-box">
                    <i class="fa-solid fa-helmet-safety"></i>
                    <span>La cuadrilla subirá la foto de reparación al concluir los trabajos en sitio.</span>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_js')
    <script src="{{ asset('js/citizen/track.js') }}?v={{ time() }}"></script>
@endsection