<?php
namespace App\Controladores\Personas;
use Liki\Modelo;
use Liki\DelegateFunction;

class Usuario extends Modelo{
  
  public static function consultar_usuario(){
      DelegateFunction::exec('ManejoUsuarios/ConsultarUsuario');
  }
  public static function editar_usuario_form($p){
      DelegateFunction::exec('ManejoUsuarios/FormularioEdicion',$p);
  }
  public static function cambiarEstado($p){
      DelegateFunction::exec('ManejoUsuarios/CambiarEstadoUsuario',$p);
  }
  public static function confirmarEliminacion($p){
      DelegateFunction::exec('ManejoUsuarios/ConfirmarEliminacion',$p);
  }
  public static function crear_usuario($p){
      DelegateFunction::exec('ManejoUsuarios/CrearUsuario',$p);
  }
  public static function eliminar_usuario($p,$f){
      DelegateFunction::exec('ManejoUsuarios/EliminarUsuario',$p,$f);
  }
  public static function editar_usuario($p,$f){
      DelegateFunction::exec('ManejoUsuarios/EditarUsuario',$p,$f);
  }
  public static function consultar_usuario_ci($p){
      DelegateFunction::exec('ManejoUsuarios/ConsultarUsuarioCI',$p);
  }
}