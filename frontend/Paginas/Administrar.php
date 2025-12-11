<?php

use Liki\Plantillas\Plantilla;
use Liki\Routing\ValidarSesion;

use Liki\Config\ConfigManager;

ValidarSesion::validar_sesion();

$config = ConfigManager::cargarConfig('Administrar');



Plantilla::HTML('estructura/pagina',$config);







