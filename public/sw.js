const CACHE_NAME = 'simpleakunting-v5-cache-v3';
const urlsToCache = [
    '/css/custom.css',
    '/images/favicon.png'
];

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
    self.skipWaiting();
});

self.addEventListener('fetch', event => {
    // Untuk halaman utama/HTML (navigasi), selalu ambil dari jaringan terlebih dahulu (Network First)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    // Jika jaringan gagal (offline), coba ambil dari cache
                    return caches.match(event.request);
                })
        );
        return;
    }

    // Untuk aset statis (CSS, JS, Gambar), gunakan Cache First
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                // Tambahkan .catch() untuk mencegah error `ERR_FAILED` jika fetch gagal
                return fetch(event.request).catch(() => {
                    console.log('Fetch failed for:', event.request.url);
                });
            })
    );
});

self.addEventListener('activate', event => {
    const cacheAllowlist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheAllowlist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});
