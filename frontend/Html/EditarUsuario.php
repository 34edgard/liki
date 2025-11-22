       
    
    
       <form 
        hx-post="/usuario/editar"
        hx-trigger="submit"
        hx-target="#usuarios"
        >
          <fieldset class="thumbnail container mt-5">
            <label class="form-label">
              cedula
              <input type="number" name="ci" class="form-control w-75" 
              value="<?= $cedula ?>"
              >
            </label>
            <label class="form-label">
              nombre
              <input type="text" name="nombre" class="form-control w-75"
              value="<?= $nombres ?>"
              >
              </label>
              <label class="form-label">
              apellido
              <input type="text" name="apellido" class= "form-control w-75"
              value="<?= $apellidos ?>"
              >
            </label>


<label class="form-label">
  nombre de usuario
  <input type="text" name="nombre_usuario" class= "form-control w-75"
  value="<?= $usuario ?>"
  >
</label>

<label class="form-label">
  correo
  <input type="email" name="correo" class= "form-control w-75"
  value="<?= $correo ?>"
  >
</label>

            <label class="form-label">
              contraseña
              <input type="password" name="contraseña"
              placeholder="este campo no es obligatorio"
              class="form-control w-75 ">
            </label>
       <?php  if($_SESSION['id_rol'] < 2): ?>
              <label>id_rol

                <select name="rol" class="form-control w-75 "
                id="editar_rol"
                hx-get="/usuario/rol"
                hx-target="#editar_rol"
                hx-trigger="load"
                >
                  


                </select>
              <?php else: ?>
              <input type="number" name="rol" hidden value="<?= $id_rol ?>">
              <?php endif ?>
              </label>
            <button type="submit" 
            data-bs-dismiss="modal" 
      
            class="btn btn-primary" 
            name="EditarUsuario" 
            value="1" >cambiar</button>

          </fieldset>
        </form>
