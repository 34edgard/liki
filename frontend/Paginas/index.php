<?php
use Liki\Plantillas\Flow;

use Liki\Config\ConfigManager;
// Uso


$config = ConfigManager::cargarConfig('Index');

Flow::html('estructura/pagina',$config);

