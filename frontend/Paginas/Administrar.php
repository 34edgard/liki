<?php

use Liki\Plantillas\Flow;
use Liki\Routing\ValidarSesion;

use Liki\Config\ConfigManager;

ValidarSesion::validar_sesion();

$config = ConfigManager::cargarConfig('Administrar');



Flow::html('estructura/pagina',$config);







