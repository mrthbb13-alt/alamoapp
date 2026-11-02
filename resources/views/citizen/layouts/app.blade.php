<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Álamo Reporta | Atención Ciudadana')</title>

    <!-- Iconos FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <!-- Estilos Base de la App Ciudadana -->
    <link rel="stylesheet" href="{{ asset('css/citizen/layout.css') }}">
    @yield('page_css')
</head>
<body>

    <div class="app-shell">
        <!-- ENCABEZADO SUPERIOR CON ESPACIO RESERVADO PARA LOGO Y MODO CLARO/OSCURO -->
        <header class="app-header">
            <div class="header-brand">
                <!-- APARTADO RESERVADO PARA EL LOGO OFICIAL -->
                <div class="logo-placeholder" title="Espacio reservado para el Logo Oficial">
                    <i class="fa-solid fa-shield-heart logo-icon"></i>
                    <span class="logo-tag">LOGO</span>
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

    <!-- Librería Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Script Base del Layout -->
    <script src="{{ asset('js/citizen/layout.js') }}"></script>
    @yield('page_js')
</body>
</html>
