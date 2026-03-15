<?php
// backend/Clases/liki/Routing/CSRF.php
namespace Liki\Routing;

class CSRF {
    
    public static function generarToken(): string {
        Sesion::init();
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public static function validarToken(?string $token): bool {
        Sesion::init();
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token ?? '');
    }
    
    public static function campo(): string {
        $token = self::generarToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
    
    public static function header(): string {
        $token = self::generarToken();
        return "X-CSRF-TOKEN: $token";
    }
}