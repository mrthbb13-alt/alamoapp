<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión | Álamo Reporta</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/citizen/layout.css') }}">
    <style>
        .login-wrap {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 24px 16px;
            background: linear-gradient(180deg, rgba(250, 204, 21, 0.12) 0%, rgba(234, 88, 12, 0.05) 100%);
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 32px 24px;
            box-shadow: var(--shadow-card);
        }
        .login-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-icon-badge {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #FACC15 0%, #EA580C 100%);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 1.8rem;
            margin-bottom: 12px;
            box-shadow: 0 8px 16px -4px rgba(234, 88, 12, 0.35);
        }
        .login-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 4px;
        }
        .login-subtitle {
            font-size: 0.88rem;
            color: var(--text-secondary);
        }
        .demo-notice {
            background: rgba(250, 204, 21, 0.18);
            border: 1px dashed var(--accent-orange);
            border-radius: 12px;
            padding: 12px 14px;
            margin-bottom: 22px;
            font-size: 0.82rem;
            line-height: 1.4;
            color: var(--text-primary);
        }
        .demo-notice strong {
            color: var(--accent-orange);
            display: block;
            margin-bottom: 3px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }
        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            color: var(--text-muted);
            font-size: 1rem;
        }
        .form-input {
            width: 100%;
            height: 48px;
            padding: 0 16px 0 42px;
            border-radius: 12px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--accent-orange);
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
        }
        .toggle-pwd-btn {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 6px;
        }
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            font-size: 0.85rem;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            cursor: pointer;
            user-select: none;
        }
        .remember-checkbox {
            width: 17px;
            height: 17px;
            accent-color: var(--accent-orange);
            cursor: pointer;
        }
        .btn-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #F59E0B 0%, #EA580C 100%);
            border: none;
            border-radius: 12px;
            color: #FFFFFF;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 18px -4px rgba(234, 88, 12, 0.35);
            transition: transform 0.15s ease, opacity 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-1px);
        }
        .btn-login:active {
            transform: translateY(1px);
        }
        .biometric-preview-box {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid var(--border-subtle);
            text-align: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }
        .biometric-badges {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 8px;
        }
        .bio-badge {
            background: var(--bg-elevated);
            border: 1px solid var(--border-subtle);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .error-alert {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #F87171;
            color: #EF4444;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon-badge">
                    <i class="fa-solid fa-shield-heart"></i>
                </div>
                <h1 class="login-title">Álamo Reporta</h1>
                <p class="login-subtitle">Atención Ciudadana y Centro de Mando</p>
            </div>

            <div class="demo-notice">
                <strong><i class="fa-solid fa-circle-info"></i> Acceso Rápido Habilitado</strong>
                Las credenciales de prueba ya están precargadas. Solo presiona <strong>"Ingresar"</strong> para continuar.
            </div>

            @if($errors->any())
                <div class="error-alert">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="username-input" class="form-label">Usuario o Correo Electrónico</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-user input-icon"></i>
                        <input type="text"
                               id="username-input"
                               name="username"
                               class="form-input"
                               value="{{ old('username', $defaultUser) }}"
                               required
                               placeholder="user o correo"
                               autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password-input" class="form-label">Contraseña</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input type="password"
                               id="password-input"
                               name="password"
                               class="form-input"
                               value="{{ $defaultPass }}"
                               required
                               placeholder="Contraseña"
                               autocomplete="current-password">
                        <button type="button" id="toggle-pwd" class="toggle-pwd-btn" aria-label="Mostrar u ocultar contraseña">
                            <i id="toggle-pwd-icon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" class="remember-checkbox" checked>
                        <span>Mantener mi sesión iniciada</span>
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Ingresar a la Plataforma</span>
                </button>
            </form>

            <div class="biometric-preview-box">
                <span>Próxima integración de autenticación oficial:</span>
                <div class="biometric-badges">
                    <span class="bio-badge"><i class="fa-solid fa-id-card"></i> INE / CURP</span>
                    <span class="bio-badge"><i class="fa-solid fa-face-viewfinder"></i> Reconocimiento Facial</span>
                    <span class="bio-badge"><i class="fa-solid fa-eye"></i> Escaneo de Iris</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-pwd');
        const pwdInput = document.getElementById('password-input');
        const pwdIcon = document.getElementById('toggle-pwd-icon');

        if (toggleBtn && pwdInput) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = pwdInput.type === 'password';
                pwdInput.type = isPassword ? 'text' : 'password';
                pwdIcon.classList.toggle('fa-eye', !isPassword);
                pwdIcon.classList.toggle('fa-eye-slash', isPassword);
            });
        }
    </script>
</body>
</html>

