// sw-simple.js
const CACHE_NAME = 'htmx-simple-cache';

self.addEventListener('fetch', (event) => {
  const request = event.request;
  
  // Solo cachear peticiones GET de HTMX
  if (request.method === 'GET' && 
      request.headers.get('HX-Request') === 'true') {
    
    event.respondWith(
      caches.open(CACHE_NAME).then(cache => {
        return cache.match(request).then(cachedResponse => {
          // Siempre intentar red primero, luego cache
          return fetch(request)
            .then(networkResponse => {
              // Guardar en cache
              cache.put(request, networkResponse.clone());
              return networkResponse;
            })
            .catch(() => {
              // Si falla la red, usar cache si existe
              return cachedResponse || 
                new Response('Error: Sin conexión', { 
                  status: 503,
                  headers: { 'Content-Type': 'text/html' }
                });
            });
        });
      })
    );
  }
});