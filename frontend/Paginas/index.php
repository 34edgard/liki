<?php
use Liki\Plantillas\Flow;
use Liki\Config\ConfigManager;


$config = ConfigManager::cargarConfig('Index');

Flow::html('estructura/pagina',$config);