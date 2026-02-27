<?php
use Liki\Routing\Ruta;
use Liki\Sesion;

return  function (){      
    Ruta::get('/Cerrar_Sesion',[Sesion::class,'cerrar_sesion']);
    Ruta::post('/iniciar/sesion',[Sesion::class,'iniciar_sesion'],['Inicio_secion','correo','contraseña']);    
};