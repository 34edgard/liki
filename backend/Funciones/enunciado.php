<?php

namespace Funciones;
class Enunciado{

public static function enunciado($op) {
  switch ($op) {
    case '0':
      return "App-Uruguay";
      break;
    case '9':
      return "App-Uruguay";
      break;
    case '1':
      return "Inicio";
      break;
    case '2':
      return "Inscripciónes";
      break;
    case '3':
      return "Docentes";
      break;

    case '4':
      return "Reportes";
      break;

    case '5':
      return "Aulas";
      break;
    case '6':
      return "Administración";
    case '7':
      return "mini php db";
      break;
  }
}

}
