const CACHE_NAME = 'edusws-cache-v1';
const urlsToCache = [
  '/',
  '/login',
  '/css/app.css',
  '/js/app.js',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png'
];

// Install event
self.addEventListener('install', function (event) {
  console.log('[SW] Installing...');
  self.skipWaiting(); // Activate this service worker immediately
  event.waitUntil(
    caches.open(CACHE_NAME).then(function (cache) {
      console.log('[SW] Pre-caching assets');
      return cache.addAll(urlsToCache);
    })
  );
});

// Activate event (cleans old caches)
self.addEventListener('activate', function (event) {
  console.log('[SW] Activating...');
  event.waitUntil(
    caches.keys().then(function (cacheNames) {
      return Promise.all(
        cacheNames.map(function (cacheName) {
          if (cacheName !== CACHE_NAME) {
            console.log('[SW] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  return self.clients.claim(); // Claim control right away
});

// Fetch event
self.addEventListener('fetch', function (event) {
  const req = event.request;

  // Only handle GET requests
  if (req.method !== 'GET') {
    return;
  }

  // Optionally skip certain URLs (e.g., API, auth routes)
  if (req.url.includes('/login') || req.url.includes('/store-fcm-token')) {
    return;
  }

  event.respondWith(
    caches.match(req).then(function (cachedResponse) {
      if (cachedResponse) {
        return cachedResponse;
      }

      return fetch(req).catch((err) => {
        console.warn('[SW] Fetch failed; returning offline fallback if any.', err);
        return new Response('You are offline.', {
          status: 503,
          statusText: 'Offline',
          headers: new Headers({ 'Content-Type': 'text/plain' })
        });
      });
    })
  );
});
