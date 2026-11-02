<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registro Ciudadano | Álamo Reporta</title>

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
            padding: 40px 24px 70px 24px;
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
            transition: transform 0.2s;
            z-index: 10;
        }

        .register-logo-circle {
            width: 76px;
            height: 76px;
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
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 3px;
        }

        .register-brand-tagline {
            font-size: 0.9rem;
            opacity: 0.92;
        }

        .register-body {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 0 20px 40px 20px;
            position: relative;
            z-index: 2;
            margin-top: -20px;
        }

        .register-card {
            width: 100%;
            max-width: 460px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 32px;
            padding: 30px 24px;
            box-shadow: 0 16px 36px -8px rgba(234, 88, 12, 0.12), 0 4px 12px -2px rgba(0, 0, 0, 0.04);
            transition: background-color 0.25s, border-color 0.25s;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 0.83rem;
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

        /* Subida de Archivo INE sin capture forzado (despliega Cámara, Galería o Archivos) */
        .file-upload-dropzone {
            display: block;
            border: 2px dashed var(--accent-orange);
            border-radius: 24px;
            padding: 20px 16px;
            background-color: var(--bg-elevated);
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
        }

        .file-upload-dropzone:hover {
            background-color: var(--accent-yellow-soft);
        }

        .file-input-hidden {
            display: none;
        }

        .upload-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .upload-icon-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #F59E0B 0%, #EA580C 100%);
            color: #FFFFFF;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 14px rgba(234, 88, 12, 0.3);
            margin-bottom: 4px;
        }

        .upload-title {
            font-size: 13.5px;
            font-weight: 800;
            color: var(--text-primary);
        }

        .upload-subtitle {
            font-size: 11.5px;
            color: var(--text-secondary);
        }

        /* Tarjeta del archivo seleccionado con nombre, preview y botón X */
        .selected-file-card {
            background: var(--bg-surface);
            border: 1.5px solid var(--accent-orange);
            border-radius: 20px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 18px rgba(234, 88, 12, 0.12);
        }

        .file-preview-thumb {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            object-fit: cover;
            cursor: pointer;
            border: 1px solid var(--border-subtle);
            flex-shrink: 0;
            transition: transform 0.15s;
        }
        .file-preview-thumb:hover {
            transform: scale(1.05);
        }

        .file-icon-thumb {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: var(--accent-yellow-soft);
            color: var(--accent-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            flex-shrink: 0;
        }

        .file-info {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .file-meta {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view-preview {
            background: none;
            border: none;
            color: var(--accent-orange);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            padding: 0;
        }

        /* Botón X para quitar archivo */
        .btn-remove-file {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.12);
            border: 1.5px solid #EF4444;
            color: #EF4444;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-remove-file:hover {
            background: #EF4444;
            color: #FFFFFF;
            transform: scale(1.08);
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
            margin-top: 10px;
            transition: transform 0.15s, box-shadow 0.2s;
        }

        .btn-pill-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px -4px rgba(234, 88, 12, 0.52);
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
        }

        /* VISOR MODAL LIGHTBOX CON FONDO TRANSPARENTE / DESENFOCADO */
        .lightbox-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.88);
            backdrop-filter: blur(12px);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .lightbox-modal.is-active {
            display: flex;
        }

        .lightbox-content-wrap {
            position: relative;
            max-width: 90vw;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .lightbox-img {
            max-width: 90vw;
            max-height: 75vh;
            border-radius: 20px;
            object-fit: contain;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .lightbox-close-btn {
            position: absolute;
            top: -18px;
            right: -18px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.9);
            color: #FFFFFF;
            border: 2px solid #FFFFFF;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            transition: transform 0.15s;
        }

        .lightbox-close-btn:hover {
            transform: scale(1.12);
        }

        .lightbox-actions {
            margin-top: 16px;
            display: flex;
            gap: 12px;
        }

        .btn-lightbox-remove {
            background: rgba(239, 68, 68, 0.2);
            border: 1.5px solid #EF4444;
            color: #EF4444;
            padding: 10px 20px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
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
                <i class="fa-solid fa-address-card"></i>
            </div>
            <h1 class="register-brand-name">Registro Ciudadano</h1>
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

                <form action="{{ route('register.post') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="name" class="form-label">Nombre Completo *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-user input-pill-icon"></i>
                            <input type="text" id="name" name="name" class="input-pill" value="{{ old('name') }}" required placeholder="Ej: Roberto Pérez">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">Nombre de Usuario *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-at input-pill-icon"></i>
                            <input type="text" id="username" name="username" class="input-pill" value="{{ old('username') }}" required placeholder="Ej: roberto26">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Teléfono o WhatsApp</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-phone input-pill-icon"></i>
                            <input type="tel" id="phone" name="phone" class="input-pill" value="{{ old('phone') }}" placeholder="765 100 0000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="colonia" class="form-label">Colonia / Localidad en Álamo *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-map-pin input-pill-icon"></i>
                            <select id="colonia" name="colonia" class="input-pill" required>
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
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-lock input-pill-icon"></i>
                            <input type="password" id="password" name="password" class="input-pill" required placeholder="Mínimo 3 caracteres">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña *</label>
                        <div class="input-pill-wrap">
                            <i class="fa-solid fa-shield-halved input-pill-icon"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="input-pill" required placeholder="Repite tu contraseña">
                        </div>
                    </div>

                    <!-- SUBIDA DE INE: CÁMARA, GALERÍA O ARCHIVOS/DOCUMENTOS -->
                    <div class="form-group">
                        <label class="form-label">Identificación INE o Documento (Opcional)</label>
                        
                        <!-- Caja para abrir el selector de archivo -->
                        <label for="ine-input" id="ine-dropzone" class="file-upload-dropzone">
                            <!-- Sin capture forzado para que el sistema permita elegir entre Cámara, Galería o Archivos -->
                            <input type="file" id="ine-input" name="ine_document" accept="image/*,application/pdf" class="file-input-hidden">
                            
                            <div class="upload-content">
                                <div class="upload-icon-circle">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                </div>
                                <span class="upload-title">Seleccionar INE o Documento</span>
                                <span class="upload-subtitle">Cámara, Galería de fotos o Archivos PDF</span>
                            </div>
                        </label>

                        <!-- Tarjeta que aparece cuando un archivo ha sido seleccionado -->
                        <div id="ine-preview-card" class="selected-file-card is-hidden">
                            <img id="ine-thumb-img" src="" alt="Vista previa" class="file-preview-thumb">
                            <div id="ine-thumb-icon" class="file-icon-thumb is-hidden">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>

                            <div class="file-info">
                                <span id="ine-file-name" class="file-name">archivo.jpg</span>
                                <div class="file-meta">
                                    <span id="ine-file-size">0 KB</span>
                                    <span>•</span>
                                    <button type="button" id="btn-open-lightbox" class="btn-view-preview">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i> Ver foto
                                    </button>
                                </div>
                            </div>

                            <!-- Botón [X] para retirar el archivo -->
                            <button type="button" id="btn-clear-ine" class="btn-remove-file" title="Quitar archivo">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-pill-submit">
                        <span>Crear mi Cuenta</span>
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

    <!-- MODAL LIGHTBOX TRANSPARENTE PARA VISUALIZAR FOTO CON BOTÓN [X] -->
    <div id="lightbox-modal" class="lightbox-modal">
        <div class="lightbox-content-wrap">
            <button type="button" id="btn-close-lightbox" class="lightbox-close-btn" title="Cerrar visor">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <img id="lightbox-img" src="" alt="Ampliación de fotografía" class="lightbox-img">
            
            <div class="lightbox-actions">
                <button type="button" id="btn-lightbox-delete" class="btn-lightbox-remove">
                    <i class="fa-solid fa-trash-can"></i> Quitar este archivo [X]
                </button>
            </div>
        </div>
    </div>

    <script>
        // Manejo de archivo INE
        const ineInput = document.getElementById('ine-input');
        const ineDropzone = document.getElementById('ine-dropzone');
        const inePreviewCard = document.getElementById('ine-preview-card');
        const ineThumbImg = document.getElementById('ine-thumb-img');
        const ineThumbIcon = document.getElementById('ine-thumb-icon');
        const ineFileName = document.getElementById('ine-file-name');
        const ineFileSize = document.getElementById('ine-file-size');
        const btnClearIne = document.getElementById('btn-clear-ine');

        // Lightbox
        const lightboxModal = document.getElementById('lightbox-modal');
        const lightboxImg = document.getElementById('lightbox-img');
        const btnOpenLightbox = document.getElementById('btn-open-lightbox');
        const btnCloseLightbox = document.getElementById('btn-close-lightbox');
        const btnLightboxDelete = document.getElementById('btn-lightbox-delete');

        let currentFileUrl = '';

        ineInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            ineFileName.textContent = file.name;
            ineFileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    currentFileUrl = evt.target.result;
                    ineThumbImg.src = currentFileUrl;
                    lightboxImg.src = currentFileUrl;
                    ineThumbImg.classList.remove('is-hidden');
                    ineThumbIcon.classList.add('is-hidden');
                    btnOpenLightbox.classList.remove('is-hidden');
                };
                reader.readAsDataURL(file);
            } else {
                currentFileUrl = '';
                ineThumbImg.classList.add('is-hidden');
                ineThumbIcon.classList.remove('is-hidden');
                btnOpenLightbox.classList.add('is-hidden');
            }

            ineDropzone.classList.add('is-hidden');
            inePreviewCard.classList.remove('is-hidden');
        });

        // Botón [X] para quitar archivo
        function removeSelectedFile() {
            ineInput.value = '';
            currentFileUrl = '';
            inePreviewCard.classList.add('is-hidden');
            ineDropzone.classList.remove('is-hidden');
            lightboxModal.classList.remove('is-active');
        }

        btnClearIne.addEventListener('click', removeSelectedFile);
        btnLightboxDelete.addEventListener('click', removeSelectedFile);

        // Abrir visor con fondo transparente
        btnOpenLightbox.addEventListener('click', function() {
            if (currentFileUrl) {
                lightboxModal.classList.add('is-active');
            }
        });

        ineThumbImg.addEventListener('click', function() {
            if (currentFileUrl) {
                lightboxModal.classList.add('is-active');
            }
        });

        // Cerrar visor
        btnCloseLightbox.addEventListener('click', function() {
            lightboxModal.classList.remove('is-active');
        });

        lightboxModal.addEventListener('click', function(e) {
            if (e.target === lightboxModal) {
                lightboxModal.classList.remove('is-active');
            }
        });

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

