<?php
namespace Middleware;
class AuthMiddleware {
    public function handle() {
        session_start();
        return isset($_SESSION['usuario_id']); // true si está autenticado  
        }
}