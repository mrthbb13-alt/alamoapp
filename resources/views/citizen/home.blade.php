@extends('citizen.layouts.app')

@section('title', 'Nuevo Reporte | Álamo Reporta')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('css/citizen/home.css') }}?v={{ time() }}">
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

    <!-- BANNER DE BIENVENIDA Y ESTADÍSTICAS RÁPIDAS ESTILO GUINDA Y ORO -->
    <section class="hero-card">
        <!-- Silueta arquitectónica municipal de fondo estilo marca de agua -->
        <svg class="hero-watermark-church" viewBox="0 0 160 100" fill="currentColor" aria-hidden="true">
            <path d="M110,95 L110,40 L105,40 L105,30 L115,30 L115,20 L118,20 L118,12 L122,12 L122,20 L125,20 L125,30 L135,30 L135,40 L130,40 L130,95 Z" opacity="0.8"/>
            <rect x="116" y="38" width="8" height="15" rx="4" fill="none" stroke="currentColor" stroke-width="2"/>
            <path d="M70,95 L70,55 Q90,45 110,55 L110,95 Z" opacity="0.6"/>
            <path d="M82,75 Q90,65 98,75 L98,95 L82,95 Z" opacity="0.4"/>
            <path d="M45,95 Q50,60 60,40 Q45,35 30,42 Q48,45 52,50 Q30,55 25,65 Q45,62 55,60 Q40,75 42,95 Z" opacity="0.7"/>
        </svg>

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

        <!-- Ondas doradas decorativas institucionales en el borde inferior -->
        <svg class="hero-gold-waves" viewBox="0 0 500 50" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,50 Q120,45 220,38 T420,15 Q465,10 500,2 L500,50 L0,50 Z" fill="#D4AF37" opacity="0.85"/>
            <path d="M0,50 Q140,48 250,40 T440,24 Q475,20 500,12 L500,50 L0,50 Z" fill="#F5D7A3" opacity="0.95"/>
        </svg>
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
                    <span class="tile-name">Alumbrado Público</span>
                    <span class="tile-dept">Servicios Municipales</span>
                </button>

                <button type="button" class="category-tile" data-category="agua">
                    <span class="tile-icon"><i class="fa-solid fa-faucet-drip"></i></span>
                    <span class="tile-name">Agua Potable</span>
                    <span class="tile-dept">CMAS</span>
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

        <!-- PASO 2: EVIDENCIA FOTOGRÁFICA, TÍTULO Y SUBTÍTULO DEL REPORTE -->
        <div class="form-step-card">
            <div class="step-header">
                <span class="step-number">2</span>
                <div>
                    <h3 class="step-title">Fotografía y detalles del reporte</h3>
                    <p class="step-hint">Toma o sube una foto o archivo y describe brevemente el problema</p>
                </div>
            </div>

            <!-- Selector de archivo con espaciado atractivo (Cámara, Galería o Documentos) -->
            <label for="evidence-input" id="photo-dropzone" class="photo-upload-box">
                <input type="file"
                       id="evidence-input"
                       name="evidence_initial"
                       accept="image/*,application/pdf,.doc,.docx"
                       class="file-input-hidden"
                       style="display: none;">
                
                <div id="photo-placeholder" class="photo-placeholder-content">
                    <div class="camera-circle">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <span class="upload-main-text">Toca para abrir cámara, galería o archivos</span>
                    <span class="upload-sub-text">Formatos JPG, PNG, WebP, PDF o Fotos</span>
                </div>
            </label>

            <!-- Tarjeta con la foto o archivo seleccionado visible en grande -->
            <div id="photo-selected-card" class="photo-preview-large-wrap" style="display: none;">
                <!-- Imagen visible grande -->
                <div id="photo-img-container" style="position: relative; width: 100%; border-radius: 16px; overflow: hidden; background: #000;">
                    <img id="photo-preview-large" src="" alt="Vista previa de la evidencia" class="photo-preview-img-large" style="width: 100%; max-height: 240px; object-fit: cover; border-radius: 16px; display: block; cursor: pointer; border: 1px solid rgba(255, 255, 255, 0.15);">
                    <button type="button" id="badge-view-fullscreen" style="position: absolute; bottom: 10px; right: 10px; background: rgba(11, 4, 11, 0.88); color: var(--accent-gold); border: 1px solid rgba(245, 215, 163, 0.4); padding: 5px 12px; border-radius: 9999px; font-size: 11.5px; font-weight: 700; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <i class="fa-solid fa-expand"></i> Ver foto completa
                    </button>
                </div>

                <!-- Si es archivo documento (PDF, etc) -->
                <div id="photo-doc-container" class="is-hidden" style="display: none; padding: 22px 14px; text-align: center; background: rgba(140, 3, 38, 0.08); border-radius: 16px;">
                    <i class="fa-solid fa-file-lines" style="font-size: 2.6rem; color: var(--accent-gold); margin-bottom: 8px; display: inline-block;"></i>
                    <span id="doc-file-name-text" style="display: block; font-size: 13.5px; font-weight: 700; color: var(--text-primary);">documento.pdf</span>
                </div>

                <div class="photo-preview-footer" style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px; gap: 10px;">
                    <div style="flex: 1; min-width: 0;">
                        <span id="photo-file-name" class="photo-file-title" style="display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">evidencia.jpg</span>
                        <span id="photo-file-size" class="photo-file-meta">0 KB</span>
                    </div>
                    <button type="button" id="btn-clear-photo" style="background: rgba(239, 68, 68, 0.12); border: 1.5px solid #EF4444; color: #DC2626; padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 6px;" title="Quitar archivo">
                        <i class="fa-solid fa-trash-can"></i> Quitar archivo
                    </button>
                </div>
            </div>

            <!-- Nota de fotografía opcional -->
            <p style="font-size: 12px; color: var(--text-secondary); margin-top: 8px; margin-bottom: 18px; text-align: center;">
                <i class="fa-solid fa-camera" style="color: var(--accent-gold); margin-right: 4px;"></i> La fotografía es opcional pero ayuda a ubicar y atender más rápido el reporte.
            </p>

            <!-- CAMPOS DE TÍTULO Y SUBTÍTULO DEL REPORTE -->
            <div class="input-group">
                <label for="input-title" class="field-label">Título del reporte *</label>
                <input type="text" id="input-title" name="title" class="field-input" placeholder="Ej: Bache profundo en la calle, Fuga de agua, Luminaria..." required>
            </div>

            <div class="input-group" style="margin-bottom: 0;">
                <label for="input-description" class="field-label">Subtítulo o descripción breve</label>
                <textarea id="input-description" name="description" rows="2" class="field-input" placeholder="Ej: Describe detalles adicionales del problema o qué riesgo genera..."></textarea>
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
                    <i class="fa-solid fa-thumbs-up"></i>
                    <span>Sumarme para darle prioridad (+1)</span>
                </button>
            </div>
        </div>

        <!-- PASO 4: LOCALIDAD Y DATOS DE REFERENCIA -->
        <div class="form-step-card">
            <div class="step-header">
                <span class="step-number">4</span>
                <div>
                    <h3 class="step-title">Colonia y referencias del lugar</h3>
                    <p class="step-hint">Ayuda a la cuadrilla municipal a encontrar la ubicación exacta</p>
                </div>
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
                <input type="text" id="input-address" name="address_reference" class="field-input" placeholder="Ej: Calle Independencia casi esquina con 5 de Mayo">
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
            <img id="home-lightbox-img" src="" alt="Ampliación de fotografía" style="max-width: 90vw; max-height: 70vh; border-radius: 20px; object-fit: contain; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6); border: 2px solid rgba(255, 255, 255, 0.2);">
            <div style="margin-top: 14px; display: flex; gap: 12px;">
                <button type="button" id="btn-lightbox-clear" style="background: rgba(239, 68, 68, 0.9); border: none; color: #FFFFFF; padding: 8px 20px; border-radius: 9999px; font-size: 13px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);">
                    <i class="fa-solid fa-trash-can"></i> Quitar fotografía
                </button>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
    <script src="{{ asset('js/citizen/home.js') }}?v={{ time() }}"></script>
@endsection
