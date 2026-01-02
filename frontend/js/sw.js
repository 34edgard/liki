// sw.js
const CACHE_NAME = 'htmx-cache-v1';
const HTMX_HEADER = 'HX-Request'; // Header que identifica peticiones HTMX

// Estrategias de cache
const CACHE_STRATEGIES = {
  NETWORK_FIRST: 'network-first',
  CACHE_FIRST: 'cache-first',
  STALE_WHILE_REVALIDATE: 'stale-while-revalidate'
};

self.addEventListener('install', (event) => {
  console.log('Service Worker instalado');
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  console.log('Service Worker activado');
  event.waitUntil(clients.claim());
});

// Intercepta todas las peticiones
self.addEventListener('fetch', (event) => {
  const request = event.request;
  
  // Solo manejar peticiones GET y que sean de HTMX
  if (request.method !== 'GET' || !isHtmxRequest(request)) {
    return; // Dejar pasar peticiones no-HTML/no-HTMX
  }
  
  // Para peticiones HTMX, usar estrategia de cache
  event.respondWith(
    handleHtmxRequest(request)
  );
});

// Verifica si es una petición HTMX
function isHtmxRequest(request) {
  // Método 1: Verificar header HX-Request
  if (request.headers.get(HTMX_HEADER) === 'true') {
    return true;
  }
  
  // Método 2: Verificar URL o parámetros específicos
  const url = new URL(request.url);
  
  // Si quieres cachear solo ciertos endpoints
  const cacheableEndpoints = [
    '/Reportes_Matricula/src',
    '/Reportes_Planilla/src'
  ];
  
  return cacheableEndpoints.some(endpoint => 
    url.pathname.includes(endpoint)
  );
}

// Manejo principal de peticiones HTMX
async function handleHtmxRequest(request) {
  const url = request.url;
  const cache = await caches.open(CACHE_NAME);
  
  try {
    // Intentar obtener de la red primero
    const networkResponse = await fetch(request);
    
    if (networkResponse.ok) {
      // Clonar la respuesta para cachearla
      const responseToCache = networkResponse.clone();
      
      // Cachear la respuesta
      event.waitUntil(
        cache.put(request, responseToCache)
          .then(() => {
            console.log(`Cacheado: ${url}`);
            
            // Notificar al cliente (opcional)
            self.clients.matchAll().then(clients => {
              clients.forEach(client => {
                client.postMessage({
                  type: 'CACHE_UPDATED',
                  url: url
                });
              });
            });
          })
      );
      
      return networkResponse;
    } else {
      // Si la red falla, intentar obtener del cache
      const cachedResponse = await cache.match(request);
      if (cachedResponse) {
        console.log(`Servido desde cache (fallback): ${url}`);
        return cachedResponse;
      }
      return networkResponse;
    }
  } catch (error) {
    // Fallback al cache si la red falla completamente
    const cachedResponse = await cache.match(request);
    if (cachedResponse) {
      console.log(`Servido desde cache (offline): ${url}`);
      return cachedResponse;
    }
    
    // Devolver una respuesta de error
    return new Response(
      JSON.stringify({ 
        error: 'Sin conexión y no hay cache disponible',
        offline: true 
      }),
      { 
        status: 503,
        headers: { 'Content-Type': 'application/json' }
      }
    );
  }
}

// Estrategia: Stale While Revalidate (más avanzada)
async function staleWhileRevalidate(request) {
  const cache = await caches.open(CACHE_NAME);
  
  // Primero devolver del cache si existe
  const cachedResponse = await cache.match(request);
  
  // En paralelo, intentar actualizar el cache
  const fetchPromise = fetch(request)
    .then(async (networkResponse) => {
      if (networkResponse.ok) {
        await cache.put(request, networkResponse.clone());
      }
      return networkResponse;
    })
    .catch(() => {
      // Ignorar errores de red en el revalidado
    });
  
  // Devolver cache inmediatamente, luego revalidar
  event.waitUntil(fetchPromise);
  
  return cachedResponse || fetchPromise;
}

// Limpiar cache antiguo
async function cleanupOldCaches() {
  const keys = await caches.keys();
  return Promise.all(
    keys.filter(key => key !== CACHE_NAME)
      .map(key => caches.delete(key))
  );
}

// Manejo de mensajes desde el cliente
self.addEventListener('message', (event) => {
  if (event.data.type === 'CLEAR_CACHE') {
    caches.delete(CACHE_NAME);
  }
  
  if (event.data.type === 'GET_CACHE_KEYS') {
    caches.open(CACHE_NAME)
      .then(cache => cache.keys())
      .then(keys => {
        event.ports[0].postMessage(keys.map(k => k.url));
      });
  }
});