
<?php
use Liki\Plantillas\Plantilla;
use Liki\Config\ConfigManager;


$config = ConfigManager::cargarConfig('Gestion_Sesion');


Plantilla::HTML('estructura/pagina',$config);




