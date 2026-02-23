const CACHE_NAME = 'liki-v2';
const ASSETS = [
  '/',
  '/manifest.json',
  // Aquí el SW puede cachear las rutas de JS que genera el builder
];

self.addEventListener('install', e => {
  e.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS)));
});

self.addEventListener('fetch', e => {
  e.respondWith(
    caches.match(e.request).then(res => res || fetch(e.request))
  );
});