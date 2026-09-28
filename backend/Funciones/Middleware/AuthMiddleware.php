<?php
namespace Middleware;
use Liki\Sesion;
class AuthMiddleware {
    protected static array $roles = [
        1=>'admin',
        2=>'user',
        3=>'dev'
    ];
    public static function isAdmin(){
        Sesion::init();
       // print_r(self::$roles[$_SESSION['id_rol']]);
          return self::$roles[$_SESSION['id_rol']] == 'admin'; // true si está autenticado  
        
    }
    public static function login() {
        Sesion::init();
        return isset($_SESSION['id_rol']); // true si está autenticado  
        }
}

