<?php
namespace App\Personas;
use Liki\Database\Tabla;
use Liki\ExecFunc;

class Usuario extends Tabla{
  public function __construct(){
    parent::__construct('usuario');
  }

  public static function consultar_usuario(){
      ExecFunc::exec('ManejoUsuarios/ConsultarUsuario');
  }


  public static function editar_usuario_form($p){
      ExecFunc::exec('ManejoUsuarios/FormularioEdicion',$p);
      
  }

  public static function cambiarEstado($p){
      ExecFunc::exec('ManejoUsuarios/CambiarEstadoUsuario',$p);
      
  }
  public static function confirmarEliminacion($p){
      ExecFunc::exec('ManejoUsuarios/ConfirmarEliminacion',$p);
      
  }

  public static function crear_usuario($p){
      ExecFunc::exec('ManejoUsuarios/CrearUsuario',$p);
      
  }

  public static function eliminar_usuario($p,$f){
      ExecFunc::exec('ManejoUsuarios/EliminarUsuario',$p,$f);
      
  }
  
  public static function editar_usuario($p,$f){
      ExecFunc::exec('ManejoUsuarios/EditarUsuario',$p,$f);
      
  }
  public static function consultar_usuario_ci($p){
      ExecFunc::exec('ManejoUsuarios/ConsultarUsuarioCI',$p);
      
  }
}
