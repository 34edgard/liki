document.body.addEventListener('htmx:beforeOnLoad', function(evt) {
    if (evt.detail.xhr.status === 401) {
        // Redirigir a la página de login
        window.location.href = '/login';
    }
});
