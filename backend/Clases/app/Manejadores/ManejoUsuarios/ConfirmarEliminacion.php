<?php

use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

   public  function run($p){  
$this->render('usuario/ConfirmarEliminacion',$p);

}
};
