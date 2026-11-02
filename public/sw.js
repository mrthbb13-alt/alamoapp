// Service Worker de Álamo Reporta para compatibilidad PWA y modo app independiente
const CACHE_NAME = 'alamo-reporta-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

self.addEventListener('fetch', (event) => {
    // Modo passthrough para garantizar que los reportes dinámicos y tokens CSRF siempre se envíen en vivo
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});

