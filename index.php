<?php
include "./conf.php";
include "./backend/autoload.php";

//phpinfo();



use Liki\Testing\TestingRutas;
use Liki\Routing\Ruta;
use Liki\Plantillas\Plantilla;
use Liki\Sesion;
use App\DatosExtra\Rol;
use App\Personas\Usuario;
use Funciones\BdSQLWeb;
use Liki\ErrorHandler;
use Liki\Database\MigrationRunner;







Ruta::get('/migracion',function(){
    
  $d = new  MigrationRunner();
    
});




Ruta::get('/testing/rutas',function(){
    TestingRutas::procesar_testing();
    
    // Mostrar interfaz de testing
    TestingRutas::mostrar_rutas_disponibles();
});



Ruta::get('/',function(){
    
  Plantilla::paginas('Gestion_Sesion');
  
});

Ruta::get('/index.php',function(){

Plantilla::paginas('Gestion_Sesion');

});


Ruta::get('/inicio',function(){
 Plantilla::paginas('inicio');
});

Ruta::get('/Administrar',function(){
  Plantilla::paginas('Administrar');
});


Ruta::get('/Cerrar_Sesion',[Sesion::class,'cerrar_sesion']);

Ruta::post('/iniciar/sesion',[Sesion::class,'iniciar_sesion'],['Inicio_secion','correo','contraseña']);



//Gestion_Usuario

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






Ruta::get('/bdSQLWeb',[
    BdSQLWeb::class,'bdSQLWeb']);





// Run the router 
Ruta::dispatch();