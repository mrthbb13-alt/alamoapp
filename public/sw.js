// Service Worker de Álamo Reporta con auto-actualización de versiones y purga de caché
const CACHE_NAME = 'alamo-reporta-v2';

self.addEventListener('install', (event) => {
    // Tomar control inmediato sin esperar al cierre de pestañas
    self.skipWaiting();
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

    // Network-First: Siempre intenta obtener la versión más reciente del servidor
    event.respondWith(
        fetch(event.request)
            .then((networkResponse) => {
                return networkResponse;
            })
            .catch(() => {
                // Si está offline, recurre a lo guardado en caché
                return caches.match(event.request);
            })
    );
});

self.addEventListener('message', (event) => {
    if (event.data && event.data.action === 'skipWaiting') {
        self.skipWaiting();
    }
});

