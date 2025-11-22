<table class="table" bordere="3">


<?php foreach ($aulas as $key => $aula):
  $num_docentes = 0;
  $num_niños =0;
?>
   <tr><th colspan="2"><?= $aula['nombre_aula'] ?></th></tr>
  <tr><td>n° docentes</td> <td><?= $num_docentes ?></td></tr>
   <tr><td>n° niños</td> <td><?= $num_niños ?></td></tr>
 
 <?php endforeach; ?>

</table>


