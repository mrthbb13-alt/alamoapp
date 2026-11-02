@extends('citizen.layouts.app')

@section('title', 'Mapa Vecinal | Álamo Reporta')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('css/citizen/feed.css') }}">
@endsection

@section('content')
    <div id="feed-config"
         data-upvote-base="{{ url('/reportar/apoyar') }}"
         class="is-hidden">
    </div>

    <section class="section-heading">
        <h2 class="page-title">Mapa Vecinal de Álamo Temapache</h2>
        <p class="page-subtitle">Explora los reportes de tu localidad y apoya con <strong>+1 Me afecta</strong> para darles prioridad.</p>
    </section>

    <!-- MAPA GENERAL DE INCIDENTES -->
    <div id="community-map" class="community-map-box"></div>

    <!-- TARJETA EMERGENTE AL TOCAR UN PIN DEL MAPA -->
    <div id="map-selected-card" class="selected-pin-card is-hidden">
        <div class="pin-card-top">
            <span id="pin-card-folio" class="folio-tag"></span>
            <button type="button" id="btn-close-pin-card" class="close-pin-btn">Cerrar</button>
        </div>
        <h4 id="pin-card-title" class="pin-card-heading"></h4>
        <p id="pin-card-location" class="pin-card-sub"></p>
    </div>

    <!-- PÍLDORAS DE FILTRO POR CATEGORÍA -->
    <div class="filter-bar">
        <button type="button" class="filter-pill is-active" data-filter="all">Todos</button>
        <button type="button" class="filter-pill" data-filter="bache">Baches</button>
        <button type="button" class="filter-pill" data-filter="drenaje">Drenaje</button>
        <button type="button" class="filter-pill" data-filter="alumbrado">Alumbrado</button>
        <button type="button" class="filter-pill" data-filter="agua">Agua</button>
        <button type="button" class="filter-pill" data-filter="accidente">Emergencias</button>
    </div>

    <!-- LISTADO DE REPORTES DE LA COMUNIDAD -->
    <section class="feed-list">
        @foreach($reports as $report)
            <article class="report-card"
                     data-id="{{ $report->id }}"
                     data-folio="{{ $report->folio }}"
                     data-title="{{ $report->title }}"
                     data-colonia="{{ $report->colonia }}"
                     data-category="{{ $report->category }}"
                     data-status="{{ $report->status }}"
                     data-lat="{{ $report->latitude }}"
                     data-lng="{{ $report->longitude }}">

                <div class="card-media">
                    <img src="{{ $report->evidence_initial }}" alt="{{ $report->title }}" class="card-img">
                    <span class="status-pill status-{{ $report->status }}">
                        {{ strtoupper(str_replace('_', ' ', $report->status)) }}
                    </span>
                </div>

                <div class="card-body">
                    <div class="card-meta-row">
                        <span class="folio-tag">{{ $report->folio }}</span>
                        <span class="dept-tag">{{ $report->department }}</span>
                    </div>

                    <h3 class="card-title">{{ $report->title }}</h3>
                    <p class="card-location">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $report->colonia }} — {{ $report->address_reference ?: 'Ubicación GPS' }}
                    </p>

                    @if($report->evidence_resolution)
                        <div class="resolved-proof-box">
                            <span class="proof-label"><i class="fa-solid fa-check-double"></i> Reparado por el Municipio:</span>
                            <img src="{{ $report->evidence_resolution }}" alt="Evidencia de reparación" class="proof-img">
                        </div>
                    @endif

                    <div class="card-footer">
                        <button type="button" class="btn-focus-map" data-lat="{{ $report->latitude }}" data-lng="{{ $report->longitude }}">
                            <i class="fa-solid fa-map-pin"></i> Ver en mapa
                        </button>

                        <button type="button" class="btn-upvote" data-id="{{ $report->id }}">
                            <i class="fa-solid fa-thumbs-up"></i>
                            <span>Me afecta (+</span><strong class="upvote-count">{{ $report->upvotes }}</strong><span>)</span>
                        </button>
                    </div>
                </div>
            </article>
        @endforeach
    </section>
@endsection

@section('page_js')
    <script src="{{ asset('js/citizen/feed.js') }}?v={{ time() }}"></script>
@endsection