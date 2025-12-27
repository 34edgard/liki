<?php


use Liki\Routing\Ruta;
use App\DatosExtra\Rol;
use App\Personas\Usuario;




return  function (){
        
     
     
     Ruta::post('/usuario/crear',[Usuario::class,'crear_usuario'],
     ['Crear_usuario','cedula','nombre','apellido','correo','usuario','rol','contraseña']);
     
     
     Ruta::get('/usuario/list',[Usuario::class,'consultar_usuario']);
     
     Ruta::get('/usuario/rol',[Rol::class,'consultar_rol'],['rol']);
     
     
     Ruta::get('/usuario/cedula',[Usuario::class,'consultar_usuario_ci'],['ci']);
     
     Ruta::get('/usuario/eliminar',[Usuario::class,'eliminar_usuario'],['eliminarUsuario','ci'],
     [[Usuario::class,'consultar_usuario']]);
     
     
     Ruta::get('/usuario/cambiarEstadoUsuario',[Usuario::class,'cambiarEstado'],['cambiarEstadoUsuario','ci']);
     
     
     Ruta::get('/usuario/form/edicion',[Usuario::class,'editar_usuario_form'],['formularioEdicion']);
     
     Ruta::get('/usuario/eliminar_confir',[Usuario::class,'confirmarEliminacion'],['confimarEliminacion']);
     
     
     Ruta::post('/usuario/editar',[Usuario::class,'editar_usuario'],['EditarUsuario','ci','nombre','nombre_usuario','correo','apellido','contraseña','rol'],[[Usuario::class,'consultar_usuario']]);
     
     
        
    
};