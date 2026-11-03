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
    <meta name="theme-color" content="#8C0326">
    <meta name="msapplication-navbutton-color" content="#8C0326">

    <!-- PREVENCIÓN DE CACHÉ ESTRICTA PARA ACTUALIZACIÓN AUTOMÁTICA -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- INICIALIZACIÓN INMEDIATA DEL TEMA (MODO CLARO POR DEFECTO AL ENTRAR) -->
    <script>
        (function() {
            try {
                var theme = sessionStorage.getItem('alamo_citizen_theme') || 'light';
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) {}
        })();
    </script>

    <!-- ICONOS PARA PANTALLA DE INICIO (iOS Safari, Android Chrome y PWA) -->
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192x192.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/favicon-32x32.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/favicon-16x16.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" href="{{ asset('icons/logo.png') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/citizen/layout.css') }}?v={{ time() }}">
    <style>
        .login-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-body);
            position: relative;
            overflow-x: hidden;
        }

        /* Cabecera institucional guinda con silueta y ondas */
        .login-hero-wave {
            position: relative;
            background: linear-gradient(135deg, #8C0326 0%, #74031F 50%, #5B0215 100%);
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
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
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
            background: rgba(0, 0, 0, 0.45);
        }

        .login-logo-circle {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            padding: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.4);
            position: relative;
        }

        .login-logo-circle::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 1.5px solid var(--accent-gold);
            opacity: 0.6;
        }

        .login-brand-name {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
            color: #FFFFFF;
        }

        .login-brand-tagline {
            font-size: 0.95rem;
            color: var(--accent-gold);
            font-weight: 600;
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
            box-shadow: var(--shadow-card);
            transition: background-color 0.25s, border-color 0.25s;
        }

        .login-card-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #FFFFFF;
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
            color: #FFFFFF;
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
            border: 1.5px solid rgba(140, 3, 38, 0.45);
            background: var(--bg-input);
            color: #FFFFFF;
            font-size: 0.98rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .input-pill:focus {
            outline: none;
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 4px rgba(245, 215, 163, 0.18);
        }

        .input-pill:focus + .input-pill-icon {
            color: var(--accent-gold);
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
            color: var(--accent-gold);
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
            accent-color: var(--accent-gold);
            cursor: pointer;
            border-radius: 5px;
        }

        /* Botón estilo píldora institucional */
        .btn-pill-submit {
            width: 100%;
            height: 54px;
            border-radius: 9999px;
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--accent-gold-dark) 100%);
            color: var(--accent-wine-deep);
            border: none;
            font-size: 1.05rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(212, 175, 55, 0.35);
            transition: transform 0.15s ease, box-shadow 0.2s ease;
            margin-top: 10px;
        }

        .btn-pill-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(212, 175, 55, 0.45);
        }

        .btn-pill-submit:active {
            transform: translateY(1px);
        }

        /* Alerta limpia si hay error */
        .error-pill {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.45);
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

        <!-- CABECERA INSTITUCIONAL CON ESCUDO OFICIAL -->
        <header class="login-hero-wave">
            <div class="login-logo-circle">
                <img id="login-brand-logo"
                     src="{{ asset('images/alamo_shield.png') }}?v={{ time() }}"
                     alt="Escudo Oficial Álamo Temapache"
                     style="width: 100%; height: 100%; object-fit: contain; display: block; filter: drop-shadow(0 3px 6px rgba(0,0,0,0.3));">
            </div>
            <h1 class="login-brand-name">Álamo Reporta</h1>
            <p class="login-brand-tagline">Gobierno Municipal 2026-2029</p>
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
                    <a href="{{ route('register') }}" style="display: block; margin-top: 8px; color: var(--accent-gold); font-weight: 800; text-decoration: none; font-size: 0.94rem;">
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

        // Modo Claro / Oscuro (Al entrar siempre inicia en modo claro)
        const htmlRoot = document.documentElement;
        const themeBtn = document.getElementById('theme-toggle-btn');
        const themeIcon = document.getElementById('theme-icon');

        const savedTheme = sessionStorage.getItem('alamo_citizen_theme') || 'light';
        applyTheme(savedTheme);

        if (themeBtn) {
            themeBtn.addEventListener('click', function () {
                const current = htmlRoot.getAttribute('data-theme') || 'light';
                const next = current === 'light' ? 'dark' : 'light';
                applyTheme(next);
                sessionStorage.setItem('alamo_citizen_theme', next);
                localStorage.setItem('alamo_citizen_theme', next);
            });
        }

        function applyTheme(theme) {
            htmlRoot.setAttribute('data-theme', theme);
            if (themeIcon) {
                if (theme === 'dark') {
                    themeIcon.classList.remove('fa-moon');
                    themeIcon.classList.add('fa-sun');
                } else {
                    themeIcon.classList.remove('fa-sun');
                    themeIcon.classList.add('fa-moon');
                }
            }
        }
    </script>
</body>
</html>
