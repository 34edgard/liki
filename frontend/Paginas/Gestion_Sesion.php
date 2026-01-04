
<?php
use Liki\Plantillas\Flow;
use Liki\Config\ConfigManager;


$config = ConfigManager::cargarConfig('Gestion_Sesion');


Flow::html('estructura/pagina',$config);




