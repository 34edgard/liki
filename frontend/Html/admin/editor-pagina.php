<?php 
extract($config);

function listText(array $array):string{
    $list = '';
    $nItems = count($array);
    
    foreach($array as $id => $item){
      $list .=  $item;
      if($id +1 < $nItems ) $list .= ',';
    }
    return $list;
}
?>
<form >
<h2>titulo de pagina</h2>
  <input type="text" name="tituloPagina" value="<?= $tituloPagina ?? '' ?>">
 

 <input type="text" name="estilos" value="<?= listText($estilosD ?? []) ?>">
  <input type="text" name="estilos" value="<?= listText($estilos ?? []) ?>">
<input type="text" name="estilos" value="<?= listText($scriptsD ?? []) ?>">
<input type="text" name="estilos" value="<?= listText($scripts ?? []) ?>">

  <?php foreach($contenidos as $contenido): ?>


<?php endforeach; ?>

 <?php print_r($config ?? [] ); ?>
</form>

