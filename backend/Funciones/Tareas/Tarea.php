<?php
use Liki\Database\FlowDB;
use Liki\Corrutina;

return (function (){
    
    $db = new FlowDB('sesiones');
    while(true){
        
        yield '666';
        $db->delete(['expira'=>['<',time()]]);
    }
})();