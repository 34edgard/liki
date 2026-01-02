
  <script>
    // Registrar Service Worker
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () => {
        navigator.serviceWorker.register('/frontend/js/sw.js')
          .then(registration => {
            console.log('Service Worker registrado:', registration.scope);
            
            // Configurar HTMX para trabajar con Service Worker
            document.body.addEventListener('htmx:configRequest', (event) => {
              // Asegurar que HTMX envíe el header
              event.detail.headers['HX-Request'] = 'true';
              
              // Agregar timestamp para evitar cache del navegador
              if (event.detail.parameters) {
                event.detail.parameters._t = Date.now();
              }
            });
          })
          .catch(error => {
            console.log('Error registrando Service Worker:', error);
          });
      });
    }
    
    // Funciones para interactuar con el Service Worker
    function clearHtmxCache() {
      if (navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({
          type: 'CLEAR_CACHE'
        });
      }
    }
    
    function getCacheStats() {
      return new Promise((resolve) => {
        const messageChannel = new MessageChannel();
        messageChannel.port1.onmessage = (event) => {
          resolve(event.data);
        };
        
        if (navigator.serviceWorker.controller) {
          navigator.serviceWorker.controller.postMessage({
            type: 'GET_CACHE_KEYS'
          }, [messageChannel.port2]);
        }
      });
    }
  </script>
