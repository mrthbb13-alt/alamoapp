document.addEventListener('DOMContentLoaded', function () {
    // 1. Control de Tema Claro / Oscuro
    const htmlRoot = document.documentElement;
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const themeIcon = document.getElementById('theme-icon');

    const savedTheme = localStorage.getItem('alamo_citizen_theme') || 'light';
    applyTheme(savedTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function () {
            const currentTheme = htmlRoot.getAttribute('data-theme');
            const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(nextTheme);
            localStorage.setItem('alamo_citizen_theme', nextTheme);
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

    // 2. Registro del Service Worker para soporte PWA y modo independiente
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function (err) {
                // Silencioso
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

    let deferredPrompt = null;

    // Capturar evento de instalación nativo en Android / Chrome
    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        if (androidInstructions) androidInstructions.classList.remove('is-hidden');
        if (iosInstructions) iosInstructions.classList.add('is-hidden');
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

        if (isIos && iosInstructions && androidInstructions) {
            iosInstructions.classList.remove('is-hidden');
            androidInstructions.classList.add('is-hidden');
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
