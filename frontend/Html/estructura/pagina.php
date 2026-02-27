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

    
        <link rel="manifest" href="/frontend/Config/manifest.json">
    
  <title>
    <?= $tituloPagina ?>
  </title>

<?php foreach($estilos as $estilo): ?>
   
    <link rel="stylesheet" href="/frontend/css/<?= $estilo ?>.css">
<?php endforeach; ?>

 <?php 
   
foreach($estilosD ?? [] as $estiloD): ?>
   
    <link rel="stylesheet" href="/<?= $estiloD ?>/css">
    <?php endforeach; ?>

<?php
 
foreach($scriptsD ?? [] as $scriptD): ?>
<script src='/<?= $scriptD ?>/js'></script>
<?php endforeach; ?>
</head>
<body hx-ext="response-targets">

<?php foreach($contenidos ?? [] as $contenido){

Flow::html($contenido['componente'],$contenido['configuracion']);

 } ?>

<?php foreach($scripts ?? [] as $script): ?>
<script src="/frontend/js/<?= $script ?>.js"></script>
<?php endforeach; ?>
</body>
</html>