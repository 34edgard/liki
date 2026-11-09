<?php

use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

   public  function run(array $p =[], array $f = []){  
$this->render('usuario/ConfirmarEliminacion',$p);

}
};
