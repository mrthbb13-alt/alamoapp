<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrarse | Álamo Reporta</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/citizen/layout.css') }}">
    <style>
        .register-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-body);
            position: relative;
            overflow-x: hidden;
        }

        .register-hero-wave {
            position: relative;
            background: linear-gradient(145deg, #FBBF24 0%, #F59E0B 40%, #EA580C 100%);
            padding: 42px 24px 75px 24px;
            text-align: center;
            color: #FFFFFF;
        }

        .register-hero-wave::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 44px;
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

        .register-logo-circle {
            width: 78px;
            height: 78px;
            border-radius: 50%;
            background: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #EA580C;
            font-size: 2rem;
            margin-bottom: 12px;
            box-shadow: 0 10px 24px -4px rgba(0, 0, 0, 0.22);
        }

        .register-brand-name {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 3px;
        }

        .register-brand-tagline {
            font-size: 0.92rem;
            opacity: 0.92;
        }

        .register-body {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 0 20px 40px 20px;
            position: relative;
            z-index: 2;
            margin-top: -22px;
        }

        .register-card {
            width: 100%;
            max-width: 460px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 32px;
            padding: 32px 24px;
            box-shadow: 0 16px 36px -8px rgba(234, 88, 12, 0.12), 0 4px 12px -2px rgba(0, 0, 0, 0.04);
            transition: background-color 0.25s, border-color 0.25s;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
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
            font-size: 1rem;
            pointer-events: none;
        }

        .input-pill {
            width: 100%;
            height: 50px;
            padding: 0 20px 0 46px;
            border-radius: 9999px;
            border: 1.5px solid var(--border-subtle);
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .input-pill:focus {
            outline: none;
            border-color: var(--accent-orange);
            background: var(--bg-surface);
            box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.14);
        }

        .btn-pill-submit {
            width: 100%;
            height: 54px;
            border-radius: 9999px;
            background: linear-gradient(135deg, #F59E0B 0%, #EA580C 100%);
            color: #FFFFFF;
            border: none;
            font-size: 1.05rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 10px 22px -5px rgba(234, 88, 12, 0.42);
            margin-top: 14px;
            transition: transform 0.15s, box-shadow 0.2s;
        }

        .btn-pill-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px -4px rgba(234, 88, 12, 0.52);
        }

        .btn-pill-submit:active {
            transform: translateY(1px);
        }

        .error-pill {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #EF4444;
            padding: 12px 18px;
            border-radius: 16px;
            font-size: 0.88rem;
            font-weight: 600;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="register-page">

        <header class="register-hero-wave">
            <button type="button" id="theme-toggle-btn" class="theme-float-btn" aria-label="Cambiar tema">
                <i id="theme-icon" class="fa-solid fa-moon"></i>
            </button>

            <div class="register-logo-circle">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 class="register-brand-name">Registrarse</h1>
            <p class="register-brand-tagline">H. Ayuntamiento de Álamo Temapache, Ver.</p>
        </header>

        <main class="register-body">
            <div class="register-card">

                @if($errors->any())
                    <div class="error-pill">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf

                    <!-- 1. NOMBRE COMPLETO (OBLIGATORIO) -->
                    <div class="form-group">
                        <label for="name" class="form-label">Nombre Completo *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-user input-pill-icon"></i>
                            <input type="text" id="name" name="name" class="input-pill" value="{{ old('name') }}" required placeholder="Ej: Roberto Pérez">
                        </div>
                    </div>

                    <!-- 2. NOMBRE DE USUARIO (OBLIGATORIO) -->
                    <div class="form-group">
                        <label for="username" class="form-label">Nombre de Usuario *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-at input-pill-icon"></i>
                            <input type="text" id="username" name="username" class="input-pill" value="{{ old('username') }}" required placeholder="Ej: roberto26">
                        </div>
                    </div>

                    <!-- 3. TELÉFONO O WHATSAPP (OBLIGATORIO) -->
                    <div class="form-group">
                        <label for="phone" class="form-label">Teléfono o WhatsApp *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-phone input-pill-icon"></i>
                            <input type="tel" id="phone" name="phone" class="input-pill" value="{{ old('phone') }}" required placeholder="Ej: 765 100 0000">
                        </div>
                    </div>

                    <!-- 4. COLONIA O LOCALIDAD (OBLIGATORIO) -->
                    <div class="form-group">
                        <label for="colonia" class="form-label">Colonia / Localidad en Álamo *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-map-pin input-pill-icon"></i>
                            <select id="colonia" name="colonia" class="input-pill" required>
                                <option value="" disabled {{ old('colonia') ? '' : 'selected' }}>Selecciona tu colonia o localidad</option>
                                <option value="Zona Centro" {{ old('colonia') === 'Zona Centro' ? 'selected' : '' }}>Zona Centro</option>
                                <option value="Col. Heroica Veracruz" {{ old('colonia') === 'Col. Heroica Veracruz' ? 'selected' : '' }}>Col. Heroica Veracruz</option>
                                <option value="Col. Pantepec" {{ old('colonia') === 'Col. Pantepec' ? 'selected' : '' }}>Col. Pantepec</option>
                                <option value="Col. Unidad y Trabajo" {{ old('colonia') === 'Col. Unidad y Trabajo' ? 'selected' : '' }}>Col. Unidad y Trabajo</option>
                                <option value="Acceso Puente López Portillo" {{ old('colonia') === 'Acceso Puente López Portillo' ? 'selected' : '' }}>Acceso Puente López Portillo</option>
                                <option value="Col. Benito Juárez" {{ old('colonia') === 'Col. Benito Juárez' ? 'selected' : '' }}>Col. Benito Juárez</option>
                                <option value="Col. Francisco I. Madero" {{ old('colonia') === 'Col. Francisco I. Madero' ? 'selected' : '' }}>Col. Francisco I. Madero</option>
                                <option value="Col. Gabino González" {{ old('colonia') === 'Col. Gabino González' ? 'selected' : '' }}>Col. Gabino González</option>
                                <option value="Otra colonia / Comunidad" {{ old('colonia') === 'Otra colonia / Comunidad' ? 'selected' : '' }}>Otra colonia / Comunidad</option>
                            </select>
                        </div>
                    </div>

                    <!-- 5. CONTRASEÑA (OBLIGATORIA) -->
                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-lock input-pill-icon"></i>
                            <input type="password" id="password" name="password" class="input-pill" required placeholder="Mínimo 3 caracteres">
                        </div>
                    </div>

                    <!-- 6. CONFIRMAR CONTRASEÑA (OBLIGATORIA) -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-shield-halved input-pill-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="input-pill" required placeholder="Repite tu contraseña">
                        </div>
                    </div>

                    <button type="submit" class="btn-pill-submit">
                        <span>Registrarse</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div style="text-align: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-subtle);">
                    <span style="font-size: 0.86rem; color: var(--text-secondary);">¿Ya tienes una cuenta registrada?</span>
                    <a href="{{ route('login') }}" style="display: block; margin-top: 8px; color: var(--accent-orange); font-weight: 800; text-decoration: none; font-size: 0.94rem;">
                        &larr; Volver a Iniciar Sesión
                    </a>
                </div>

            </div>
        </main>
    </div>

    <script>
        // Tema Claro / Oscuro
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
