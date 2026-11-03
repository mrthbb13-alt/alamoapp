document.addEventListener('DOMContentLoaded', function () {
    // 1. Control de Tema Claro / Oscuro (Al entrar siempre inicia en Modo Claro por defecto)
    const htmlRoot = document.documentElement;
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const themeIcon = document.getElementById('theme-icon');

    // Al entrar a la app siempre inicia en modo claro por defecto
    const savedTheme = sessionStorage.getItem('alamo_citizen_theme') || 'light';
    applyTheme(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            const currentTheme = htmlRoot.getAttribute('data-theme') || 'light';
            const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(nextTheme);
            sessionStorage.setItem('alamo_citizen_theme', nextTheme);
            localStorage.setItem('alamo_citizen_theme', nextTheme);
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

        if (themeToggleBtn) {
            const label = theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
            themeToggleBtn.setAttribute('title', label);
            themeToggleBtn.setAttribute('aria-label', label);
        }
    }

    // 2. Registro del Service Worker y Auto-Refresco de Cachés para Nuevas Versiones
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js', { updateViaCache: 'none' })
                .then(function (reg) {
                    // Forzar comprobación inmediata de actualizaciones de versión
                    reg.update();

                    if (reg.waiting) {
                        reg.waiting.postMessage({ action: 'skipWaiting' });
                    }

                    reg.addEventListener('updatefound', function () {
                        const newWorker = reg.installing;
                        if (newWorker) {
                            newWorker.addEventListener('statechange', function () {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // Nueva versión detectada e instalada: solicitar activación
                                    newWorker.postMessage({ action: 'skipWaiting' });
                                }
                            });
                        }
                    });
                })
                .catch(function (err) {
                    // Silencioso
                });

            // Al tomar control la nueva versión, refrescar automáticamente para aplicar cambios
            let isRefreshing = false;
            navigator.serviceWorker.addEventListener('controllerchange', function () {
                if (!isRefreshing) {
                    isRefreshing = true;
                    window.location.reload();
                }
            });
        });
    }

    // 3. Detección de Modo App Nativa (Standalone) vs Navegador
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent);
    const isStandalone = (window.navigator.standalone === true) || window.matchMedia('(display-mode: standalone)').matches;

    const pwaBanner = document.getElementById('pwa-install-banner');
    const pwaModal = document.getElementById('pwa-instructions-modal');
    const btnShowModal = document.getElementById('btn-show-pwa-modal');
    const btnCloseBanner = document.getElementById('btn-close-pwa-banner');
    const btnCloseModal = document.getElementById('btn-close-pwa-modal');
    const btnUnderstood = document.getElementById('btn-understood-pwa');
    const iosInstructions = document.getElementById('pwa-ios-instructions');
    const androidInstructions = document.getElementById('pwa-android-instructions');
    const btnAndroidInstall = document.getElementById('btn-android-install-trigger');
    const tabAndroid = document.getElementById('tab-pwa-android');
    const tabIos = document.getElementById('tab-pwa-ios');

    let deferredPrompt = null;

    // Control de pestañas Android / iOS en el modal de instalación
    function selectPlatform(platform) {
        if (platform === 'ios') {
            if (tabIos) tabIos.classList.add('is-active');
            if (tabAndroid) tabAndroid.classList.remove('is-active');
            if (iosInstructions) iosInstructions.classList.remove('is-hidden');
            if (androidInstructions) androidInstructions.classList.add('is-hidden');
        } else {
            if (tabAndroid) tabAndroid.classList.add('is-active');
            if (tabIos) tabIos.classList.remove('is-active');
            if (androidInstructions) androidInstructions.classList.remove('is-hidden');
            if (iosInstructions) iosInstructions.classList.add('is-hidden');
        }
    }

    if (tabAndroid) {
        tabAndroid.addEventListener('click', function () {
            selectPlatform('android');
        });
    }

    if (tabIos) {
        tabIos.addEventListener('click', function () {
            selectPlatform('ios');
        });
    }

    // Auto-seleccionar según dispositivo detectado
    if (isIos) {
        selectPlatform('ios');
    } else {
        selectPlatform('android');
    }

    // Capturar evento de instalación nativo en Android / Chrome
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        if (!isStandalone && pwaBanner && sessionStorage.getItem('pwa_banner_dismissed') !== 'true') {
            pwaBanner.classList.remove('is-hidden');
        }
    });

    // Si no está en modo standalone (sigue en navegador Safari/Chrome), mostrar banner
    if (!isStandalone && pwaBanner) {
        const wasDismissed = sessionStorage.getItem('pwa_banner_dismissed') === 'true';
        if (!wasDismissed) {
            pwaBanner.classList.remove('is-hidden');
        }
    }

    if (btnShowModal && pwaModal) {
        btnShowModal.addEventListener('click', function () {
            pwaModal.classList.add('is-active');
        });
    }

    function closeModal() {
        if (pwaModal) pwaModal.classList.remove('is-active');
    }

    if (btnCloseModal) btnCloseModal.addEventListener('click', closeModal);
    if (btnUnderstood) btnUnderstood.addEventListener('click', closeModal);

    if (pwaModal) {
        pwaModal.addEventListener('click', function (e) {
            if (e.target === pwaModal) closeModal();
        });
    }

    if (btnCloseBanner && pwaBanner) {
        btnCloseBanner.addEventListener('click', function () {
            pwaBanner.classList.add('is-hidden');
            sessionStorage.setItem('pwa_banner_dismissed', 'true');
        });
    }

    if (btnAndroidInstall) {
        btnAndroidInstall.addEventListener('click', function () {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function (choice) {
                    if (choice.outcome === 'accepted') {
                        if (pwaBanner) pwaBanner.classList.add('is-hidden');
                    }
                    deferredPrompt = null;
                    closeModal();
                });
            }
        });
    }

    // 4. Asegurar que los enlaces internos se mantengan dentro del modo App en iOS
    if (isStandalone) {
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a');
            if (link && link.href && link.target !== '_blank') {
                const url = new URL(link.href, window.location.origin);
                if (url.origin === window.location.origin) {
                    e.preventDefault();
                    window.location.href = link.href;
                }
            }
        }, false);
    }
});
