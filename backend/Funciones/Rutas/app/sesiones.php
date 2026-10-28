<?php
use Liki\Routing\Ruta;
use App\Controladores\Sesiones;


return  function (){      

    Ruta::get('/cerrar/sesion',[Sesiones::class,'cerrarSesion']);
    Ruta::post('/iniciar/sesion',[Sesiones::class,'iniciarSesion'],['correo','contraseña']);    
};