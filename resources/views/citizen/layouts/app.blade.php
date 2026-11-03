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
    <meta name="theme-color" content="#EA580C">
    <meta name="msapplication-navbutton-color" content="#EA580C">

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
        <!-- ENCABEZADO SUPERIOR CON LOGO OFICIAL Y MODO CLARO/OSCURO -->
        <header class="app-header">
            <div class="header-brand">
                <div class="logo-placeholder" title="Álamo Reporta">
                    <img src="{{ asset('icons/logo.png') }}?v={{ time() }}"
                         data-light="{{ asset('icons/logo.png') }}?v={{ time() }}"
                         data-dark="{{ asset('icons/logo-dark.png') }}?v={{ time() }}"
                         alt="Álamo Reporta"
                         class="brand-logo-img">
                </div>
                <div class="brand-titles">
                    <span class="brand-locality">Álamo Temapache, Ver.</span>
                    <h1 class="brand-name">Reporte Ciudadano</h1>
                </div>
            </div>

            <div class="header-actions">
                @auth
                    <a href="{{ route('citizen.profile') }}" class="theme-btn" title="Mi Perfil: {{ Auth::user()->name }}" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-circle-user" style="color: var(--accent-orange); font-size: 1.15rem;"></i>
                    </a>
                @endauth
                <button type="button" id="theme-toggle-btn" class="theme-btn" aria-label="Cambiar modo de color">
                    <i id="theme-icon" class="fa-solid fa-moon"></i>
                </button>
            </div>
        </header>

        <!-- BANNER DISCRETO DE INSTALACIÓN (Solo visible si se abre en el navegador Safari/Chrome y no en modo app) -->
        <div id="pwa-install-banner" class="pwa-install-banner is-hidden" style="margin: 14px 16px 0 16px;">
            <div class="pwa-banner-left">
                <img src="{{ asset('icons/logo.png') }}?v={{ time() }}"
                     data-light="{{ asset('icons/logo.png') }}?v={{ time() }}"
                     data-dark="{{ asset('icons/logo-dark.png') }}?v={{ time() }}"
                     alt="App Icon"
                     class="pwa-banner-icon brand-logo-img">
                <div>
                    <span class="pwa-banner-text">Instalar como App</span>
                    <span class="pwa-banner-sub">Úsala a pantalla completa sin barras</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
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
                <i class="fa-solid fa-camera-retro nav-icon"></i>
                <span class="nav-label">Reportar</span>
            </a>

            <a href="{{ route('citizen.feed') }}" class="nav-item {{ request()->routeIs('citizen.feed') ? 'is-active' : '' }}">
                <i class="fa-solid fa-map-location-dot nav-icon"></i>
                <span class="nav-label">Mapa Vecinal</span>
            </a>

            <a href="{{ route('citizen.track') }}" class="nav-item {{ request()->routeIs('citizen.track') ? 'is-active' : '' }}">
                <i class="fa-solid fa-magnifying-glass-location nav-icon"></i>
                <span class="nav-label">Mi Folio</span>
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
                    <img src="{{ asset('icons/logo.png') }}?v={{ time() }}"
                         data-light="{{ asset('icons/logo.png') }}?v={{ time() }}"
                         data-dark="{{ asset('icons/logo-dark.png') }}?v={{ time() }}"
                         alt="Logo"
                         class="brand-logo-img"
                         style="width: 32px; height: 32px; border-radius: 50%; object-fit: contain;">
                    <span>Instalar Álamo Reporta</span>
                </div>
                <button type="button" id="btn-close-pwa-modal" style="background: none; border: none; font-size: 1.2rem; color: var(--text-muted); cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Contenido dinámico según el sistema (iOS o Android) -->
            <div id="pwa-ios-instructions">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 14px;">Para usar la aplicación a pantalla completa en tu iPhone o iPad sin barras de Safari:</p>
                
                <div class="pwa-step">
                    <div class="pwa-step-num">1</div>
                    <div class="pwa-step-desc">
                        Presiona el botón <span class="pwa-step-highlight">Compartir</span> en la barra inferior de Safari (<i class="fa-solid fa-arrow-up-from-bracket" style="color: var(--accent-orange);"></i>).
                    </div>
                </div>

                <div class="pwa-step">
                    <div class="pwa-step-num">2</div>
                    <div class="pwa-step-desc">
                        Baja y selecciona la opción <span class="pwa-step-highlight">"Agregar a pantalla de inicio"</span> (<i class="fa-solid fa-square-plus" style="color: var(--accent-orange);"></i>).
                    </div>
                </div>

                <div class="pwa-step">
                    <div class="pwa-step-num">3</div>
                    <div class="pwa-step-desc">
                        Toca <span class="pwa-step-highlight">"Agregar"</span> en la esquina superior derecha. ¡Listo! Se abrirá desde tu pantalla como una app nativa con el logo oficial.
                    </div>
                </div>
            </div>

            <div id="pwa-android-instructions" class="is-hidden">
                <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 14px;">Para instalar Álamo Reporta en tu dispositivo Android:</p>
                <button type="button" id="btn-android-install-trigger" class="btn-pill-submit" style="margin-bottom: 12px; height: 48px;">
                    <i class="fa-solid fa-download"></i> Instalar Aplicación Ahora
                </button>
                <p style="font-size: 12px; color: var(--text-muted); text-align: center;">O toca los 3 puntos (&#8942;) del navegador y elige <strong>"Instalar aplicación"</strong> o <strong>"Agregar a la pantalla principal"</strong>.</p>
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
