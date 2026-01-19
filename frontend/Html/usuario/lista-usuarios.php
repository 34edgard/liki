<?php
use App\Controladores\DatosExtra\Correo;
$usuarioEstado = "activo";
$estadoEstilo = "secondary";
  if ($estado == "activo") {
    $usuarioEstado = "desactivo";
    $estadoEstilo = "success";
  } 




  $cedula_codificada = urlencode($cedula);
  $url_hx_get = "/usuario/cambiarEstadoUsuario?cambiarEstadoUsuario&ci={$cedula_codificada}";
  ?>
  
    <tr >
      <td><?= $cedula ?></td>
      <td><?= $nombres ?></td>
      <td><?= $apellidos ?></td>
      <td><?= Correo::optenerEmail($id_correo) ?></td>
      <td
      id="estado<?= $cedula ?>" 
      ><button class="btn btn-<?= $estadoEstilo ?>" 
      
      
      hx-get="<?= $url_hx_get ?>"
     hx-target="#estado<?= $cedula ?>" 
      hx-trigger='click'
      ><?= $estado ?></button></td>
  <td><button class='btn btn-warning'
 data-bs-toggle='modal' data-bs-target='#firefoxModal'
  name="formularioEdicion"
  value="<?= $cedula; ?>"
  hx-get="/usuario/form/edicion"
  hx-trigger="click"
  hx-target="#modal-form"
  >editar</button></td>

<?php  if ($_SESSION["id_rol"] == 1): ?>
<td><button class='btn btn-danger' 
    data-bs-toggle='modal' data-bs-target='#firefoxModal'
    name="confimarEliminacion"
  value="<?= $cedula ?>"
  hx-get="/usuario/eliminar_confir"
  hx-trigger="click"
  hx-target="#modal-form"
    >eliminar</button></td>
  
<?php endif; ?>
  </tr>
