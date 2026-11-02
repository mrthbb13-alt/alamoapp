<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión | Álamo Reporta</title>

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
    <link rel="icon" type="image/svg+xml" href="{{ asset('icons/icon.svg') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/citizen/layout.css') }}">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-body);
            position: relative;
            overflow-x: hidden;
        }

        /* Cabecera ondulada orgánica inspirada en TravelApp */
        .login-hero-wave {
            position: relative;
            background: linear-gradient(145deg, #FBBF24 0%, #F59E0B 40%, #EA580C 100%);
            padding: calc(48px + env(safe-area-inset-top, 0px)) 24px 80px 24px;
            text-align: center;
            color: #FFFFFF;
        }

        .login-hero-wave::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 48px;
            background-color: var(--bg-body);
            clip-path: ellipse(58% 100% at 50% 100%);
            transition: background-color 0.25s ease;
        }

        .theme-float-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #FFFFFF;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, background-color 0.2s;
            z-index: 10;
        }
        .theme-float-btn:hover {
            transform: scale(1.08);
            background: rgba(255, 255, 255, 0.35);
        }

        .login-logo-circle {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            background: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #EA580C;
            font-size: 2.3rem;
            margin-bottom: 14px;
            box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.22);
            position: relative;
        }

        .login-logo-circle::before {
            content: '';
            position: absolute;
            inset: -5px;
            border-radius: 50%;
            border: 2px dashed rgba(255, 255, 255, 0.7);
        }

        .login-brand-name {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .login-brand-tagline {
            font-size: 0.95rem;
            opacity: 0.92;
            font-weight: 500;
        }

        /* Contenedor flotante del formulario */
        .login-body {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 0 20px 40px 20px;
            position: relative;
            z-index: 2;
            margin-top: -24px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 32px;
            padding: 34px 28px;
            box-shadow: 0 16px 36px -8px rgba(234, 88, 12, 0.12), 0 4px 12px -2px rgba(0, 0, 0, 0.04);
            transition: background-color 0.25s, border-color 0.25s;
        }

        .login-card-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 6px;
            text-align: center;
        }

        .login-card-subtitle {
            font-size: 0.88rem;
            color: var(--text-secondary);
            text-align: center;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 7px;
            padding-left: 6px;
        }

        .input-pill-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-pill-icon {
            position: absolute;
            left: 18px;
            color: var(--text-muted);
            font-size: 1.05rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .input-pill {
            width: 100%;
            height: 52px;
            padding: 0 20px 0 48px;
            border-radius: 9999px;
            border: 1.5px solid var(--border-subtle);
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: 0.98rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .input-pill:focus {
            outline: none;
            border-color: var(--accent-orange);
            background: var(--bg-surface);
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14);
        }

        .input-pill:focus + .input-pill-icon {
            color: var(--accent-orange);
        }

        .toggle-pwd-btn {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 8px;
            font-size: 1rem;
            transition: color 0.2s;
        }

        .toggle-pwd-btn:hover {
            color: var(--accent-orange);
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 6px;
            font-size: 0.86rem;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
            font-weight: 500;
        }

        .remember-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--accent-orange);
            cursor: pointer;
            border-radius: 5px;
        }

        /* Botón estilo píldora TravelApp */
        .btn-pill-submit {
            width: 100%;
            height: 54px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #F59E0B 0%, #EA580C 100%);
            color: #FFFFFF;
            border: none;
            font-size: 1.05rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 10px 22px -5px rgba(234, 88, 12, 0.42);
            transition: transform 0.15s ease, box-shadow 0.2s ease, opacity 0.2s;
            margin-top: 10px;
        }

        .btn-pill-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px -4px rgba(234, 88, 12, 0.52);
        }

        .btn-pill-submit:active {
            transform: translateY(1px);
        }

        /* Alerta limpia únicamente si hay error */
        .error-pill {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #EF4444;
            padding: 12px 18px;
            border-radius: 16px;
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }
    </style>
</head>
<body>
    <div class="login-page">

        <!-- CABECERA ONDULADA CON LOGO Y BOTÓN DE MODO CLARO / OSCURO -->
        <header class="login-hero-wave">
            <button type="button" id="theme-toggle-btn" class="theme-float-btn" aria-label="Cambiar tema">
                <i id="theme-icon" class="fa-solid fa-moon"></i>
            </button>

            <div class="login-logo-circle" style="padding: 0; overflow: hidden; border: none;">
                <img src="{{ asset('icons/icon.svg') }}" alt="Álamo Reporta" style="width: 100%; height: 100%; border-radius: 50%; display: block;">
            </div>
            <h1 class="login-brand-name">Álamo Reporta</h1>
            <p class="login-brand-tagline">Atención Ciudadana y Respuesta Inmediata</p>
        </header>

        <!-- TARJETA FLOTANTE CON CAMPOS ESTILO TRAVELAPP -->
        <main class="login-body">
            <div class="login-card">
                <h2 class="login-card-title">Bienvenido(a)</h2>
                <p class="login-card-subtitle">Inicia sesión para reportar o dar seguimiento a tus reportes</p>

                @if($errors->has('login_error') || $errors->any())
                    <div class="error-pill">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $errors->first('login_error') ?: 'Usuario o contraseña incorrectos.' }}</span>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="username" class="form-label">Usuario o Correo</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-user input-pill-icon"></i>
                            <input type="text"
                                   id="username"
                                   name="username"
                                   class="input-pill"
                                   value="{{ old('username', $defaultUser) }}"
                                   required
                                   autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-lock input-pill-icon"></i>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="input-pill"
                                   value="{{ $defaultPass }}"
                                   required
                                   autocomplete="current-password">
                            <button type="button" id="toggle-pwd" class="toggle-pwd-btn" aria-label="Mostrar/ocultar contraseña">
                                <i id="toggle-pwd-icon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="remember-wrap">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" class="remember-checkbox" checked>
                            <span>Mantener mi sesión iniciada</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-pill-submit">
                        <span>Iniciar Sesión</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-subtle);">
                    <span style="font-size: 0.86rem; color: var(--text-secondary);">¿Aún no tienes una cuenta?</span>
                    <a href="{{ route('register') }}" style="display: block; margin-top: 8px; color: var(--accent-orange); font-weight: 800; text-decoration: none; font-size: 0.94rem;">
                        Registrarse &rarr;
                    </a>
                </div>
            </div>
        </main>
    </div>

    <!-- Script de Cambio de Contraseña y Tema -->
    <script>
        // Mostrar / Ocultar contraseña
        const toggleBtn = document.getElementById('toggle-pwd');
        const pwdInput = document.getElementById('password');
        const pwdIcon = document.getElementById('toggle-pwd-icon');

        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                pwdIcon.classList.toggle('fa-eye', !isPassword);
                pwdIcon.classList.toggle('fa-eye-slash', isPassword);
            });
        }

        // Modo Claro / Oscuro con persistencia en localStorage
        const htmlRoot = document.documentElement;
        const themeBtn = document.getElementById('theme-toggle-btn');
        const themeIcon = document.getElementById('theme-icon');

        const savedTheme = localStorage.getItem('alamo_citizen_theme') || 'light';
        applyTheme(savedTheme);

        if (themeBtn) {
            themeBtn.addEventListener('click', function () {
                const current = htmlRoot.getAttribute('data-theme');
                const next = current === 'light' ? 'dark' : 'light';
                applyTheme(next);
                localStorage.setItem('alamo_citizen_theme', next);
            });
        }

        function applyTheme(theme) {
            htmlRoot.setAttribute('data-theme', theme);
            if (!themeIcon) return;
            if (theme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
    </script>
</body>
</html>
