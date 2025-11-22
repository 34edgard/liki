<?php
use Liki\Plantillas\Plantilla;
?>

<!DOCTYPE html>
  <html lang="es">
<head>
  <meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="generator" content="Hugo 0.88.1">
    <link rel="shortcut icon" href="/frontend/Img/Logo.jpg" type="image/x-icon" />
  <title>
    <?= $tituloPagina ?>
  </title>

<?php foreach($estilos as $estilo): ?>
   
    <link rel="stylesheet" href="/frontend/css/<?= $estilo ?>.css">
<?php endforeach; ?>

 <?php 

     $estilosD = $estilosDinamocos ?? [];
     
foreach($estilosD as $estiloD): ?>
       

    <link rel="stylesheet" href="/frontend/css/<?= $estiloD ?>.php">
    <?php endforeach; ?>

</head>
<body>



<?php foreach($contenidos as $contenido){

Plantilla::HTML($contenido['componente'],$contenido['configuracion']);

 } ?>


<?php

     $scriptsD = $scriptsDinamocos ?? [];
     
 foreach($scriptsD as $scriptD): ?>
<script src="/frontend/js/<?= $scriptD ?>.php"></script>
<?php endforeach; ?>

<?php foreach($scripts as $script): ?>
<script src="/frontend/js/<?= $script ?>.js"></script>
<?php endforeach; ?>
</body>

</html>
