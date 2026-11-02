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

    <!-- BANNER DE BIENVENIDA Y ESTADÍSTICAS RÁPIDAS ESTILO TRAVELAPP -->
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

        <!-- PASO 1: SELECCIÓN DE CATEGORÍA VISUAL (ÍCONOS CIRCULARES) -->
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
                    <span class="tile-icon"><i class="fa-solid fa-road"></i></span>
                    <span class="tile-name">Bache o Calle</span>
                    <span class="tile-dept">Obras Públicas</span>
                </button>

                <button type="button" class="category-tile" data-category="drenaje">
                    <span class="tile-icon"><i class="fa-solid fa-water"></i></span>
                    <span class="tile-name">Tapa / Drenaje</span>
                    <span class="tile-dept">CAEV / Drenaje</span>
                </button>

                <button type="button" class="category-tile" data-category="alumbrado">
                    <span class="tile-icon"><i class="fa-solid fa-lightbulb"></i></span>
                    <span class="tile-name">Luminaria</span>
                    <span class="tile-dept">Alumbrado</span>
                </button>

                <button type="button" class="category-tile" data-category="agua">
                    <span class="tile-icon"><i class="fa-solid fa-faucet-drip"></i></span>
                    <span class="tile-name">Fuga de Agua</span>
                    <span class="tile-dept">Agua Potable</span>
                </button>

                <button type="button" class="category-tile" data-category="basura">
                    <span class="tile-icon"><i class="fa-solid fa-trash-can"></i></span>
                    <span class="tile-name">Basura / Limpia</span>
                    <span class="tile-dept">Limpia Pública</span>
                </button>

                <button type="button" class="category-tile tile-emergency" data-category="accidente">
                    <span class="tile-icon"><i class="fa-solid fa-car-burst"></i></span>
                    <span class="tile-name">Accidente / Riesgo</span>
                    <span class="tile-dept">Protección Civil</span>
                </button>
            </div>
        </div>

        <!-- PASO 2: EVIDENCIA FOTOGRÁFICA (SIN CAPTURE FORZADO, CON NOMBRE, PREVIEW Y [X]) -->
        <div class="form-step-card">
            <div class="step-header">
                <span class="step-number">2</span>
                <div>
                    <h3 class="step-title">Toma o sube una fotografía</h3>
                    <p class="step-hint">Cámara, Galería de fotos o Archivos</p>
                </div>
            </div>

            <!-- Selector de archivo (sin capture="environment" para permitir elegir origen) -->
            <label for="evidence-input" id="photo-dropzone" class="photo-upload-box">
                <input type="file" id="evidence-input" name="evidence_initial" accept="image/*" class="file-input-hidden" required>
                
                <div id="photo-placeholder" class="photo-placeholder-content">
                    <div class="camera-circle">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <span class="upload-main-text">Toca para abrir cámara, galería o archivos</span>
                    <span class="upload-sub-text">Formatos JPG, PNG, WebP o Fotos</span>
                </div>
            </label>

            <!-- Tarjeta con el archivo seleccionado, nombre, tamaño y botón [X] -->
            <div id="photo-selected-card" class="selected-file-card is-hidden" style="background: var(--bg-surface); border: 1.5px solid var(--accent-orange); border-radius: 20px; padding: 12px 14px; display: none; align-items: center; gap: 12px; box-shadow: 0 8px 18px rgba(234, 88, 12, 0.12);">
                <img id="photo-preview-thumb" src="" alt="Vista previa" style="width: 58px; height: 58px; border-radius: 14px; object-fit: cover; cursor: pointer; border: 1.5px solid var(--border-subtle); flex-shrink: 0;">
                
                <div class="file-info" style="flex: 1; min-width: 0;">
                    <span id="photo-file-name" style="font-size: 13px; font-weight: 700; color: var(--text-primary); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">evidencia.jpg</span>
                    <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px; display: flex; align-items: center; gap: 6px;">
                        <span id="photo-file-size">0 KB</span>
                        <span>•</span>
                        <button type="button" id="btn-view-photo-lightbox" style="background: none; border: none; color: var(--accent-orange); font-size: 11.5px; font-weight: 700; cursor: pointer; padding: 0;">
                            <i class="fa-solid fa-magnifying-glass-plus"></i> Ver foto
                        </button>
                    </div>
                </div>

                <!-- Botón [X] para retirar el archivo -->
                <button type="button" id="btn-clear-photo" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(239, 68, 68, 0.12); border: 1.5px solid #EF4444; color: #EF4444; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 14px; flex-shrink: 0;" title="Quitar fotografía">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
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
                <span><i class="fa-solid fa-map-pin"></i> Coordenadas:</span>
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
                    @php $userColonia = Auth::user()->colonia ?? 'Zona Centro'; @endphp
                    <option value="Zona Centro" {{ $userColonia === 'Zona Centro' ? 'selected' : '' }}>Zona Centro</option>
                    <option value="Col. Heroica Veracruz" {{ $userColonia === 'Col. Heroica Veracruz' ? 'selected' : '' }}>Col. Heroica Veracruz</option>
                    <option value="Col. Pantepec" {{ $userColonia === 'Col. Pantepec' ? 'selected' : '' }}>Col. Pantepec</option>
                    <option value="Col. Unidad y Trabajo" {{ $userColonia === 'Col. Unidad y Trabajo' ? 'selected' : '' }}>Col. Unidad y Trabajo</option>
                    <option value="Acceso Puente López Portillo" {{ $userColonia === 'Acceso Puente López Portillo' ? 'selected' : '' }}>Acceso Puente López Portillo</option>
                    <option value="Col. Benito Juárez" {{ $userColonia === 'Col. Benito Juárez' ? 'selected' : '' }}>Col. Benito Juárez</option>
                    <option value="Col. Francisco I. Madero" {{ $userColonia === 'Col. Francisco I. Madero' ? 'selected' : '' }}>Col. Francisco I. Madero</option>
                    <option value="Col. Gabino González" {{ $userColonia === 'Col. Gabino González' ? 'selected' : '' }}>Col. Gabino González</option>
                    <option value="Otra colonia / Comunidad" {{ $userColonia === 'Otra colonia / Comunidad' ? 'selected' : '' }}>Otra colonia / Comunidad</option>
                </select>
            </div>

            <div class="input-group">
                <label for="input-address" class="field-label">Calle y referencia visual</label>
                <input type="text" id="input-address" name="address_reference" class="field-input" placeholder="Ej: Calle Independencia frente a la escuela">
            </div>

            <div class="input-group">
                <label for="input-description" class="field-label">Descripción adicional (Opcional)</label>
                <textarea id="input-description" name="description" rows="2" class="field-input" placeholder="Describe desde cuándo está el problema o el riesgo que representa..."></textarea>
            </div>

            <div class="citizen-optional-grid">
                <div class="input-group">
                    <label for="input-name" class="field-label">Tu Nombre</label>
                    <input type="text" id="input-name" name="citizen_name" class="field-input" value="{{ Auth::user()->name ?? session('citizen_name', '') }}" placeholder="Anónimo si lo dejas vacío">
                </div>
                <div class="input-group">
                    <label for="input-phone" class="field-label">WhatsApp / Teléfono</label>
                    <input type="tel" id="input-phone" name="citizen_phone" class="field-input" value="{{ Auth::user()->phone ?? session('citizen_phone', '') }}" placeholder="Ej: 765 100 0000">
                </div>
            </div>

            <p id="form-error-msg" class="error-banner is-hidden"></p>

            <button type="submit" id="btn-submit-report" class="btn-submit-main">
                <i class="fa-solid fa-paper-plane"></i>
                <span id="btn-submit-text">Enviar Reporte al Municipio</span>
            </button>
        </div>
    </form>

    <!-- MODAL LIGHTBOX TRANSPARENTE PARA VISUALIZAR FOTO CON BOTÓN [X] -->
    <div id="home-lightbox-modal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.88); backdrop-filter: blur(12px); z-index: 9999; flex-direction: column; align-items: center; justify-content: center; padding: 20px;">
        <div style="position: relative; max-width: 90vw; max-height: 80vh; display: flex; flex-direction: column; align-items: center;">
            <button type="button" id="btn-close-home-lightbox" style="position: absolute; top: -18px; right: -18px; width: 44px; height: 44px; border-radius: 50%; background: rgba(239, 68, 68, 0.95); color: #FFFFFF; border: 2px solid #FFFFFF; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0,0,0,0.5);" title="Cerrar visor">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <img id="home-lightbox-img" src="" alt="Ampliación de fotografía" style="max-width: 90vw; max-height: 75vh; border-radius: 20px; object-fit: contain; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6); border: 2px solid rgba(255, 255, 255, 0.2);">
        </div>
    </div>
@endsection

@section('page_js')
    <script src="{{ asset('js/citizen/home.js') }}?v={{ time() }}"></script>
@endsection
