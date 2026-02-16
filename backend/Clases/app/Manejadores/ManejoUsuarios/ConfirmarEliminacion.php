<?php
use Liki\Plantillas\Flow;
return new class {
   public static function run($p){  
Flow::html('usuario/ConfirmarEliminacion',$p);

}
};
