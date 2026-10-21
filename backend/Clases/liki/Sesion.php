<?php
namespace Liki;


class Sesion {
  public static function init() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }
}