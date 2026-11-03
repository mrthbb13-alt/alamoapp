<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Álamo Reporta | Atención Ciudadana')</title>

    <!-- METATAGS PWA Y PANTALLA COMPLETA SIN NAVEGADOR (iOS y Android) -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Álamo Reporta">
    <meta name="application-name" content="Álamo Reporta">
    <meta name="theme-color" content="#8C0326">
    <meta name="msapplication-navbutton-color" content="#8C0326">

    <!-- ICONOS PARA PANTALLA DE INICIO (iOS Safari, Android Chrome y PWA) -->
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/favicon-16x16.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" href="{{ asset('icons/logo.png') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">

    <!-- Iconos FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <!-- Estilos Base de la App Ciudadana -->
    <link rel="stylesheet" href="{{ asset('css/citizen/layout.css') }}?v={{ time() }}">
    @yield('page_css')
</head>
<body>

    <div class="app-shell">
        <!-- ENCABEZADO SUPERIOR INSTITUCIONAL GUINDA CON ESCUDO OFICIAL -->
        <header class="app-header">
            <div class="header-brand">
                <img src="{{ asset('images/alamo_shield.png') }}?v={{ time() }}"
                     alt="Escudo Municipal Álamo Temapache"
                     class="brand-crest-shield">
                <div class="header-brand-divider"></div>
                <div class="brand-titles">
                    <span class="brand-name-city">ÁLAMO</span>
                    <span class="brand-name-sub">TEMAPACHE</span>
                    <span class="brand-slogan">GOBIERNO MUNICIPAL 2026-2029</span>
                </div>
            </div>

            <div class="header-actions">
                <a href="{{ route('citizen.profile') }}" class="header-settings-btn" aria-label="Ajustes y Perfil" title="Ajustes y Perfil">
                    <i class="fa-solid fa-gear"></i>
                </a>
            </div>
        </header>

        <!-- BANNER DE INSTALACIÓN PWA -->
        <div id="pwa-install-banner" class="pwa-install-banner is-hidden">
            <div class="pwa-banner-left">
                <div class="pwa-banner-phone-icon">
                    <i class="fa-solid fa-mobile-screen"></i>
                </div>
                <div class="pwa-banner-info">
                    <span class="pwa-banner-text">Instalar como App</span>
                    <span class="pwa-banner-sub">Úsala a pantalla completa sin barras</span>
                </div>
            </div>
            <div class="pwa-banner-right">
                <button type="button" id="btn-show-pwa-modal" class="pwa-banner-btn">
                    <i class="fa-solid fa-arrow-down"></i> Instalar
                </button>
                <button type="button" id="btn-close-pwa-banner" class="pwa-banner-close" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL DE CADA RAMA -->
        <main class="app-content">
            @yield('content')
        </main>

        <!-- BARRA DE NAVEGACIÓN INFERIOR (ESTILO APP MÓVIL) -->
        <nav class="bottom-nav">
            <a href="{{ route('citizen.home') }}" class="nav-item {{ request()->routeIs('citizen.home') ? 'is-active' : '' }}">
                <i class="fa-solid fa-camera nav-icon"></i>
                <span class="nav-label">Reportar</span>
            </a>

            <a href="{{ route('citizen.feed') }}" class="nav-item {{ request()->routeIs('citizen.feed') ? 'is-active' : '' }}">
                <i class="fa-solid fa-map-location-dot nav-icon"></i>
                <span class="nav-label">Mapa</span>
            </a>

            <a href="{{ route('citizen.track') }}" class="nav-item {{ request()->routeIs('citizen.track') ? 'is-active' : '' }}">
                <i class="fa-solid fa-magnifying-glass-location nav-icon"></i>
                <span class="nav-label">Mi Folio</span>
            </a>

            <a href="{{ route('citizen.directory') }}" class="nav-item {{ request()->routeIs('citizen.directory') ? 'is-active' : '' }}">
                <i class="fa-solid fa-address-book nav-icon"></i>
                <span class="nav-label">Directorio</span>
            </a>

            <a href="{{ route('citizen.profile') }}" class="nav-item {{ request()->routeIs('citizen.profile') ? 'is-active' : '' }}">
                <i class="fa-solid fa-user-gear nav-icon"></i>
                <span class="nav-label">Perfil</span>
            </a>
        </nav>
    </div>

    <!-- MODAL DE INSTRUCCIONES PARA AGREGAR A PANTALLA DE INICIO (iOS y Android) -->
    <div id="pwa-instructions-modal" class="pwa-modal-backdrop">
        <div class="pwa-modal-card">
            <div class="pwa-modal-header">
                <div class="pwa-modal-title">
                    <img src="{{ asset('images/alamo_shield.png') }}?v={{ time() }}"
                         alt="Escudo Oficial"
                         class="brand-crest-shield"
                         style="width: 32px; height: 32px; object-fit: contain;">
                    <span>Instalar Álamo Reporta</span>
                </div>
                <button type="button" id="btn-close-pwa-modal" style="background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- División por pestañas para Android y para iOS -->
            <div class="pwa-platform-tabs">
                <button type="button" id="tab-pwa-android" class="pwa-platform-tab is-active">
                    <i class="fa-brands fa-android"></i> Android
                </button>
                <button type="button" id="tab-pwa-ios" class="pwa-platform-tab">
                    <i class="fa-brands fa-apple"></i> iPhone / iOS
                </button>
            </div>

            <!-- Contenido para Android -->
            <div id="pwa-android-instructions">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 14px;">Pasos para instalar en teléfonos Samsung, Xiaomi, Motorola, etc.:</p>

                <button type="button" id="btn-android-install-trigger" class="btn-pill-submit" style="margin-bottom: 14px; height: 46px; font-size: 13px;">
                    <i class="fa-solid fa-download"></i> Instalar Aplicación Ahora
                </button>

                <div class="pwa-step">
                    <div class="pwa-step-num">1</div>
                    <div class="pwa-step-desc">
                        Abre el menú de Chrome tocando los <span class="pwa-step-highlight">3 puntos (⋮)</span> en la esquina superior derecha.
                    </div>
                </div>

                <div class="pwa-step">
                    <div class="pwa-step-num">2</div>
                    <div class="pwa-step-desc">
                        Selecciona la opción <span class="pwa-step-highlight">"Instalar aplicación"</span> o <span class="pwa-step-highlight">"Agregar a la pantalla principal"</span>.
                    </div>
                </div>

                <div class="pwa-step">
                    <div class="pwa-step-num">3</div>
                    <div class="pwa-step-desc">
                        Presiona <span class="pwa-step-highlight">"Instalar"</span>. ¡Listo! Quedará en tu menú de aplicaciones con el escudo oficial.
                    </div>
                </div>
            </div>

            <!-- Contenido para iOS (iPhone / iPad) -->
            <div id="pwa-ios-instructions" class="is-hidden">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 14px;">Pasos para usar a pantalla completa en tu iPhone o iPad sin barras de Safari:</p>
                
                <div class="pwa-step">
                    <div class="pwa-step-num">1</div>
                    <div class="pwa-step-desc">
                        Presiona el botón <span class="pwa-step-highlight">Compartir</span> en la barra inferior de Safari (<i class="fa-solid fa-arrow-up-from-bracket" style="color: var(--accent-gold);"></i>).
                    </div>
                </div>

                <div class="pwa-step">
                    <div class="pwa-step-num">2</div>
                    <div class="pwa-step-desc">
                        Desliza y selecciona la opción <span class="pwa-step-highlight">"Agregar a pantalla de inicio"</span> (<i class="fa-solid fa-square-plus" style="color: var(--accent-gold);"></i>).
                    </div>
                </div>

                <div class="pwa-step">
                    <div class="pwa-step-num">3</div>
                    <div class="pwa-step-desc">
                        Toca <span class="pwa-step-highlight">"Agregar"</span> en la esquina superior derecha. ¡Listo! Se abrirá desde tu pantalla como una app nativa con el escudo oficial.
                    </div>
                </div>
            </div>

            <button type="button" id="btn-understood-pwa" class="pwa-modal-close-btn">
                Entendido
            </button>
        </div>
    </div>

    <!-- Librería Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Script Base del Layout y Soporte PWA -->
    <script src="{{ asset('js/citizen/layout.js') }}?v={{ time() }}"></script>
    @yield('page_js')
</body>
</html>
