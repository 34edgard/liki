<?php

use Liki\Plantillas\Flow;
use Liki\Routing\ValidarSesion;

use Liki\Config\ConfigManager;



ValidarSesion::validar_sesion();


$config = ConfigManager::cargarConfig('Inicio');







Flow::html('estructura/pagina',$config);





