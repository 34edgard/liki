<h2>Realmente desea eliminar al usuario  <?= $confimarEliminacion ?></h2>
   <button class='btn btn-danger' 
data-bs-toggle='modal' data-bs-target='#firefoxModal'
name="ci"
      value="<?= $confimarEliminacion ?>"
      hx-get="/usuario/eliminar?eliminarUsuario"
      hx-trigger="click"
      hx-target="#usuarios"
>Si</button>