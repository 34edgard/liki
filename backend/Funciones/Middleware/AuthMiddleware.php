<?php
namespace Middleware;
use liki\Sesion;
class AuthMiddleware {
    public function handle() {
        Sesion::init();
        return isset($_SESSION['usuario_id']); // true si está autenticado  
        }
}