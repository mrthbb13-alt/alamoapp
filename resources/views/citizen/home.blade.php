@extends('citizen.layouts.app')

@section('title', 'Nuevo Reporte | Álamo Reporta')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('css/citizen/home.css') }}">
@endsection

@section('content')
    <!-- Configuración de Endpoints para JS sin mezclar código -->
    <div id="home-config"
         data-store-url="{{ route('citizen.store') }}"
         data-nearby-url="{{ route('citizen.nearby') }}"
         data-upvote-base="{{ url('/reportar/apoyar') }}"
         data-track-url="{{ route('citizen.track') }}"
         class="is-hidden">
    </div>

    <!-- BANNER DE BIENVENIDA Y ESTADÍSTICAS RÁPIDAS -->
    <section class="hero-card">
        <div class="hero-text">
            <span class="hero-badge">Atención Directa Municipal</span>
            <h2 class="hero-title">¿Qué sucede hoy en tu calle o colonia?</h2>
            <p class="hero-subtitle">Envía foto y ubicación en menos de 30 segundos. Tu reporte llega en vivo al Centro de Mando.</p>
        </div>
        <div class="hero-metrics">
            <div class="metric-box">
                <span class="metric-number">{{ $recentCount }}</span>
                <span class="metric-label">Reportes Activos</span>
            </div>
            <div class="metric-box metric-box-highlight">
                <span class="metric-number">{{ $resolvedCount }}</span>
                <span class="metric-label">Atendidos</span>
            </div>
        </div>
    </section>

    <!-- ALERTA DE ÉXITO AL ENVIAR REPORTE (OCULTA POR DEFECTO) -->
    <section id="success-banner" class="success-card is-hidden">
        <div class="success-icon-wrap">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h3 class="success-title">¡Reporte Recibido en el Municipio!</h3>
        <p class="success-desc">Guarda tu número de folio oficial para consultar el avance y ver la foto de reparación:</p>
        <div id="created-folio-box" class="folio-pill">ALM-2026-0000</div>
        <div class="success-actions">
            <a id="btn-go-track" href="{{ route('citizen.track') }}" class="btn-primary">
                <i class="fa-solid fa-magnifying-glass-location"></i> Dar Seguimiento a mi Folio
            </a>
            <button type="button" id="btn-new-report" class="btn-secondary">
                Enviar otro reporte
            </button>
        </div>
    </section>

    <!-- FORMULARIO GUIADO DE REPORTE CIUDADANO -->
    <form id="citizen-report-form" class="report-form" enctype="multipart/form-data">

        <!-- PASO 1: SELECCIÓN DE CATEGORÍA VISUAL -->
        <div class="form-step-card">
            <div class="step-header">
                <span class="step-number">1</span>
                <div>
                    <h3 class="step-title">Selecciona el tipo de incidente</h3>
                    <p class="step-hint">Se asignará automáticamente al departamento correspondiente</p>
                </div>
            </div>

            <input type="hidden" id="selected-category" name="category" value="bache">

            <div class="category-grid">
                <button type="button" class="category-tile is-selected" data-category="bache">
                    <i class="fa-solid fa-road tile-icon"></i>
                    <span class="tile-name">Bache o Calle</span>
                    <span class="tile-dept">Obras Públicas</span>
                </button>

                <button type="button" class="category-tile" data-category="drenaje">
                    <i class="fa-solid fa-water tile-icon"></i>
                    <span class="tile-name">Tapa / Drenaje</span>
                    <span class="tile-dept">CAEV / Drenaje</span>
                </button>

                <button type="button" class="category-tile" data-category="alumbrado">
                    <i class="fa-solid fa-lightbulb tile-icon"></i>
                    <span class="tile-name">Luminaria</span>
                    <span class="tile-dept">Alumbrado</span>
                </button>

                <button type="button" class="category-tile" data-category="agua">
                    <i class="fa-solid fa-faucet-drip tile-icon"></i>
                    <span class="tile-name">Fuga de Agua</span>
                    <span class="tile-dept">Agua Potable</span>
                </button>

                <button type="button" class="category-tile" data-category="basura">
                    <i class="fa-solid fa-trash-can tile-icon"></i>
                    <span class="tile-name">Basura / Limpia</span>
                    <span class="tile-dept">Limpia Pública</span>
                </button>

                <button type="button" class="category-tile tile-emergency" data-category="accidente">
                    <i class="fa-solid fa-car-burst tile-icon"></i>
                    <span class="tile-name">Accidente / Riesgo</span>
                    <span class="tile-dept">Protección Civil</span>
                </button>
            </div>
        </div>

        <!-- PASO 2: EVIDENCIA FOTOGRÁFICA -->
        <div class="form-step-card">
            <div class="step-header">
                <span class="step-number">2</span>
                <div>
                    <h3 class="step-title">Toma o sube una fotografía</h3>
                    <p class="step-hint">Muestra claramente el desperfecto o incidente</p>
                </div>
            </div>

            <label for="evidence-input" id="photo-dropzone" class="photo-upload-box">
                <input type="file" id="evidence-input" name="evidence_initial" accept="image/*" capture="environment" class="file-input-hidden" required>
                
                <div id="photo-placeholder" class="photo-placeholder-content">
                    <div class="camera-circle">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <span class="upload-main-text">Toca aquí para abrir la cámara o galería</span>
                    <span class="upload-sub-text">Formatos JPG, PNG o WebP</span>
                </div>

                <div id="photo-preview-container" class="photo-preview-wrap is-hidden">
                    <img id="photo-preview-img" src="" alt="Vista previa de evidencia" class="photo-preview-image">
                    <span class="change-photo-badge">
                        <i class="fa-solid fa-rotate"></i> Cambiar fotografía
                    </span>
                </div>
            </label>
        </div>

        <!-- PASO 3: UBICACIÓN GPS EN ÁLAMO Y DETECCIÓN DE DUPLICADOS -->
        <div class="form-step-card">
            <div class="step-header">
                <span class="step-number">3</span>
                <div>
                    <h3 class="step-title">Ubicación del incidente</h3>
                    <p class="step-hint">Mueve el marcador o usa el GPS de tu teléfono</p>
                </div>
            </div>

            <button type="button" id="btn-locate-me" class="btn-gps">
                <i class="fa-solid fa-location-crosshairs"></i> Usar mi ubicación GPS actual
            </button>

            <div id="citizen-map" class="map-box"></div>

            <div class="coords-bar">
                <span><i class="fa-solid fa-map-pin"></i> Coordenadas seleccionadas:</span>
                <strong id="coords-display">20.9114, -97.6744</strong>
            </div>

            <input type="hidden" id="input-lat" name="latitude" value="20.9114000">
            <input type="hidden" id="input-lng" name="longitude" value="-97.6744000">

            <!-- ALERTA INTELIGENTE DE REPORTE DUPLICADO CERCANO -->
            <div id="duplicate-alert" class="duplicate-box is-hidden">
                <div class="duplicate-header">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>¡Un vecino ya reportó este incidente cerca de aquí!</span>
                </div>
                <p id="dup-title" class="duplicate-title"></p>
                <p id="dup-meta" class="duplicate-meta"></p>
                <button type="button" id="btn-upvote-existing" class="btn-upvote-dup">
                    <i class="fa-solid fa-thumbs-up"></i> Sumarme para darle prioridad (+1 Me afecta)
                </button>
            </div>
        </div>

        <!-- PASO 4: DETALLES RÁPIDOS DE REFERENCIA -->
        <div class="form-step-card">
            <div class="step-header">
                <span class="step-number">4</span>
                <div>
                    <h3 class="step-title">Datos de referencia</h3>
                    <p class="step-hint">Ayuda a la cuadrilla municipal a encontrarlo más rápido</p>
                </div>
            </div>

            <div class="input-group">
                <label for="input-title" class="field-label">Título breve del problema *</label>
                <input type="text" id="input-title" name="title" class="field-input" placeholder="Ej: Tapa de drenaje rota a media calle" required>
            </div>

            <div class="input-group">
                <label for="input-colonia" class="field-label">Colonia o Localidad en Álamo *</label>
                <select id="input-colonia" name="colonia" class="field-input" required>
                    <option value="Zona Centro">Zona Centro</option>
                    <option value="Col. Heroica Veracruz">Col. Heroica Veracruz</option>
                    <option value="Col. Pantepec">Col. Pantepec</option>
                    <option value="Col. Unidad y Trabajo">Col. Unidad y Trabajo</option>
                    <option value="Acceso Puente López Portillo">Acceso Puente López Portillo</option>
                    <option value="Col. Benito Juárez">Col. Benito Juárez</option>
                    <option value="Col. Francisco I. Madero">Col. Francisco I. Madero</option>
                    <option value="Col. Gabino González">Col. Gabino González</option>
                    <option value="Otra colonia / Comunidad">Otra colonia / Comunidad</option>
                </select>
            </div>

            <div class="input-group">
                <label for="input-address" class="field-label">Calle y referencia visual</label>
                <input type="text" id="input-address" name="address_reference" class="field-input" placeholder="Ej: Calle Independencia frente a la escuela primaria">
            </div>

            <div class="input-group">
                <label for="input-description" class="field-label">Descripción adicional (Opcional)</label>
                <textarea id="input-description" name="description" rows="2" class="field-input" placeholder="Describe desde cuándo está el problema o el riesgo que representa..."></textarea>
            </div>

            <div class="citizen-optional-grid">
                <div class="input-group">
                    <label for="input-name" class="field-label">Tu Nombre (Opcional)</label>
                    <input type="text" id="input-name" name="citizen_name" class="field-input" value="{{ Auth::user()->name ?? '' }}" placeholder="Anónimo si lo dejas vacío">
                </div>
                <div class="input-group">
                    <label for="input-phone" class="field-label">WhatsApp / Teléfono</label>
                    <input type="tel" id="input-phone" name="citizen_phone" class="field-input" value="{{ Auth::user()->phone ?? '' }}" placeholder="Ej: 765 100 0000">
                </div>
            </div>

            <p id="form-error-msg" class="error-banner is-hidden"></p>

            <button type="submit" id="btn-submit-report" class="btn-submit-main">
                <i class="fa-solid fa-paper-plane"></i>
                <span id="btn-submit-text">Enviar Reporte al Municipio</span>
            </button>
        </div>
    </form>
@endsection

@section('page_js')
    <script src="{{ asset('js/citizen/home.js') }}"></script>
@endsection