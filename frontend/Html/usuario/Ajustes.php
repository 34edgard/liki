<div class="container mt-5">
  <ul class="nav nav-tabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tablaUsuarios" role="tab">usuarios</button>
    </li>
    <?php if ($_SESSION['id_rol'] < 2):
      ?>
      <li class="nav-item" role="presentation">
        <button type="button" class="nav-link " data-bs-toggle="tab" data-bs-target="#general" role="tab" aria-selected="true">reguistro de usuarios</button>
      </li>
      <?php endif ?>

  </ul>

  <div class="tab-content" role="tablist">
    <?php if ($_SESSION['id_rol'] < 2):
      ?>
      <div class="tab-pane  table-responsive" id="general" role="tabpanel">
     <form 
       hx-post="/usuario/crear"
       hx-trigger="submit"
       hx-target="#modal-form"
     >
       <fieldset class="container mt-5 p-4 border rounded shadow-sm">
         <legend class="mb-4 text-center">Crear Nuevo Usuario</legend>
     
         <div class="row mb-3">
           <div class="col-md-6">
             <label for="cedula" class="form-label">Cédula</label>
             <input type="number" name="cedula" id="cedula" class="form-control" placeholder="Ingrese la cédula"  minlength="8"
             maxlength="11"  required>
           </div>
           <div class="col-md-6">
             <label for="nombre" class="form-label">Nombre</label>
             <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ingrese el nombre"  minlength="3"  required>
           </div>
         </div>
     
         <div class="row mb-3">
           <div class="col-md-6">
             <label for="apellido" class="form-label">Apellido</label>
             <input type="text" name="apellido" id="apellido" class="form-control" placeholder="Ingrese el apellido">
           </div>
           <div class="col-md-6">
             <label for="usuario" class="form-label">Nombre de Usuario</label>
             <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingrese el nombre de usuario">
           </div>
         </div>
     
         <div class="row mb-3">
           <div class="col-md-6">
             <label for="correo" class="form-label">Correo</label>
             <input type="email" name="correo" id="correo" class="form-control" placeholder="Ingrese el correo electrónico">
           </div>
           <div class="col-md-6">
             <?php if ($_SESSION["id_rol"] < 2) : ?>
               <label for="crear_user_rol" class="form-label">Rol</label>
               <select name="rol" class="form-select"
                 hx-target="#crear_user_rol"
                 id="crear_user_rol"
                 hx-get="/usuario/rol"
                 hx-trigger="load"
               >
                 <option value="">Seleccione un rol</option>
               </select>
             <?php else: ?>
               <input type="number" name="rol" value="2" hidden>
             <?php endif ?>
           </div>
         </div>
     
         <div class="row mb-4">
           <div class="col-12">
             <label for="contrasena" class="form-label">Contraseña</label>
             <input type="password" name="contraseña" id="contrasena" class="form-control" placeholder="Ingrese la contraseña"  minlength="8" required>
           </div>
         </div>
     
         <div class="d-grid gap-2 d-md-flex justify-content-md-end">
           <button type="reset" class="btn btn-danger me-md-2">Borrar</button>
           <button type="submit" class="btn btn-primary" 
             data-bs-toggle='modal' data-bs-target='#firefoxModal'
             name="Crear_usuario" value="crear" id="formulario-crearUsuario">Registrar</button>
         </div>
       </fieldset>
     </form>

      </div>
      <?php endif ?>
    <div class="tab-pane active table-responsive"  role="tabpanel" id="tablaUsuarios">
      <table class="table table-bordered">
        <tr>
          <td>cedula</td>
          <td>nombres</td>
          <td>apellidos</td>
          
          <td>correo</td>
          <td>estado</td>
          <td colspan="2"></td>
        </tr>
<tbody id="usuarios"
hx-get="/usuario/list"
hx-trigger="load" 
hx-target="#usuarios" 

  >
  
</tbody>
      </table>


    </div>

  </div>
</div>



<div class="modal fade" id="firefoxModal" tabindex="-1" aria-labelledby="firefoxModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-body " id="modal-form">



      </div>

    </div>
  </div>
</div>
<div class="modal fade" id="mres" tabindex="-1" aria-labelledby="firefoxModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-body " id="res">



      </div>

    </div>
  </div>
</div>



<div class="container" id="mesajesDelServidor"></div>

