<?php

use Liki\Plantillas\Flow;
use Middleware\AuthMiddleware as Auth;
$titulo =  $op ?? "";

?>

<header class="d-flex flex-wrap align-items-center bg-primary justify-content-center justify-content-md-between py-3 mb-4 border-bottom px-3">
    <a href="/" class=" row   col-md-2 mb-2 mb-md-0 text-white text-decoration-none " id="pagina-seccion">
        <img src="/frontend/Img/Logo.jpg" style="width:100px; height:80px" class="rounded-circle bi  ">
        <h3 class="col" id="titulo">
            <?= htmlspecialchars($titulo) ?>
        </h3>
        <div class="col" id='parrafo'></div>
    </a>

    <?php if (Auth::login()): ?>
       
        <?php Flow::html('componentes/navs'); ?>
    <?php endif; ?>

</header>

<?php Flow::html('componentes/modo-oscuro'); // Assuming this component exists ?>

<main id="main" class="container">
  
