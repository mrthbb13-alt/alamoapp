// Service Worker de Álamo Reporta v5.2 - Carga Instantánea de Activos y Modo Offline
const CACHE_NAME = 'alamo-reporta-v5.2';

const STATIC_PRECACHE = [
    '/css/citizen/layout.css?v=5.2',
    '/js/citizen/layout.js?v=5.2',
    '/images/alamo_shield.png?v=5.2',
    '/manifest.json?v=5.2',
];

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_PRECACHE).catch(() => {});
        })
    );
});

self.addEventListener('activate', (event) => {
    // Purgar automáticamente todos los cachés antiguos para que se carguen los archivos nuevos
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    // Si no es petición GET o es petición dinámica (formularios, API, CSRF), ir directo a la red
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);

    // Si es un activo estático (imágenes, CSS, JS, fuentes), usar Cache-First para carga en 0ms
    const isStaticAsset = url.pathname.match(/\.(png|jpe?g|svg|css|js|woff2?|ico)$/i) ||
                          url.hostname.includes('cdnjs.cloudflare.com') ||
                          url.hostname.includes('fonts.googleapis.com') ||
                          url.hostname.includes('fonts.gstatic.com');

    if (isStaticAsset) {
        event.respondWith(
            caches.match(event.request).then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(() => caches.match(event.request));
            })
        );
        return;
    }

    // Para páginas HTML y rutas dinámicas, usar Network-First
    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200 && event.request.mode === 'navigate') {
                    const responseToCache = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                }
                return networkResponse;
            })
            .catch(() => {
                return caches.match(event.request);
            })
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.action === 'skipWaiting') {
        self.skipWaiting();
    }
});

