<?php
namespace Middleware;
use Liki\Sesion;
class AuthMiddleware {
    public static function login() {
        Sesion::init();
       // print_r($_SESSION);
        return isset($_SESSION['id_rol']); // true si está autenticado  
        }
}

