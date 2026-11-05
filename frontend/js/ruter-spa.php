// frontend/js/router.php
const routes = <?= json_encode($rutas) ?>;
window.addEventListener('popstate', render);
function render() {
    const path = location.pathname;
    const match = routes.find(r => r.path === path);
    if (match) document.getElementById('app').innerHTML = match.html;
}