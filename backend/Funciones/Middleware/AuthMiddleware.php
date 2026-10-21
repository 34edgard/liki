<?php
namespace Middleware;
use Liki\Sesion;
class AuthMiddleware {
    protected static array $roles = [
        1=>'admin',
        2=>'dev',
        3=>'user',
        4=>'client'
    ];
    public static function isAdmin():bool{
        Sesion::init();
       return self::login() && (self::$roles[$_SESSION['id_rol']] ?? null) == 'admin'; // true si está autenticado  
        
    }
    public static function isUser():bool{
        Sesion::init();
       return self::login() && (self::$roles[$_SESSION['id_rol']] ?? null) == 'user'; // true si está autenticado  
        
    }
    public static function isDev():bool{
        Sesion::init();
       return self::login() && (self::$roles[$_SESSION['id_rol']] ?? null) == 'dev'; // true si está autenticado  
        
    }
    public static function isClient():bool{
        Sesion::init();
       return self::login() && (self::$roles[$_SESSION['id_rol']] ?? null) == 'client'; // true si está autenticado  
        
    }
    public static function login():bool {
        Sesion::init();
        return isset($_SESSION['id_rol']); // true si está autenticado  
        }
}

