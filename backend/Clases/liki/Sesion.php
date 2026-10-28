<?php
namespace Liki;


class Sesion {
  public static function init() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }
  public static function get(string $name, $defaul=null){
      self::init();
     if(!isset($_SESSION[$name]))
         $_SESSION[$name] = $defaul;
    return $_SESSION[$name];
  }
  public static function set(string $name, $defaul =null){
      self::init();
     $_SESSION[$name] = $defaul;
  }
}