<?php
namespace Middleware;
use Liki\Sesion;
class AuthMiddleware {
    protected static array $roles = [
        1=>'admin',
        2=>'dev',
        3=>'user',
        4=>'clien'
    ];
    public static function isAdmin():bool{
        Sesion::init();
       return self::$roles[$_SESSION['id_rol']] == 'admin'; // true si está autenticado  
        
    }
    public static function isUser():bool{
        Sesion::init();
       return self::$roles[$_SESSION['id_rol']] == 'user'; // true si está autenticado  
        
    }
    public static function isDev():bool{
        Sesion::init();
       return self::$roles[$_SESSION['id_rol']] == 'dev'; // true si está autenticado  
        
    }
    public static function isClien():bool{
        Sesion::init();
       return self::$roles[$_SESSION['id_rol']] == 'clien'; // true si está autenticado  
        
    }
    public static function login():bool {
        Sesion::init();
        return isset($_SESSION['id_rol']); // true si está autenticado  
        }
}

