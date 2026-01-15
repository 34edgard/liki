<?php
use Liki\Plantillas\Flow;
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



<script>
<?php
    $scriptsD = $scriptsDinamocos ?? [];
foreach($scriptsD as $scriptD): ?>
<?php Flow::js($scriptD); ?>
<?php endforeach; ?>
</script>

</head>
<body>



<?php foreach($contenidos as $contenido){

Flow::html($contenido['componente'],$contenido['configuracion']);

 } ?>






<?php foreach($scripts as $script): ?>
<script src="/frontend/js/<?= $script ?>.js"></script>
<?php endforeach; ?>
</body>

</html>
