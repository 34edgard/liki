<?php
use Liki\Plantillas\Flow;
// This component handles the main navigation when logged in

$menu = 'componentes/menu-desplegable';
?>
<ul class="nav col-8 col-md-auto mb-2 justify-content-center mb-md-0 gap-3">
    <li>
        <a href="/inicio" class="nav-link px-2 text-white">Inicio</a>
    </li>

    <li>
        <?php
        Flow::html($menu, [
            'title' => 'Reportes',
            'items' => [
                ['label' => 'option1', 'hx_get' => '/Reportes_Matricula/src', 'hx_target' => '#main', 'hx_swap' => 'innerHTML', 'hx_trigger' => 'click', 'onclick' => "option1','Matricula"],
                ['label' => 'option2', 'hx_get' => '/Reportes_Planilla/src', 'hx_target' => '#main', 'hx_swap' => 'innerHTML', 'hx_trigger' => 'click',
                'onclick_title' => "Reportes','Planilla"],
              
            ]
        ]);
        ?>
    </li>

    <li>
        <div class="dropdown">
            <a href="#" class="d-block link-body-emphasis link-white text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="frontend/Img/images.png" alt="mdo" width="32" height="32" class="rounded-circle border border-white">
            </a>
            <ul class="dropdown-menu text-small shadow dropdown-menu-end ">
                <li><a href="/Administrar" class="dropdown-item text-warning">Administrar</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a type="button" class="dropdown-item text-danger" id="cerrarSesion"
                       hx-get="/Cerrar_Sesion"
                       hx-trigger="click"
                       hx-target="#cerrarSecion"
                    >Cerrar Sesión</a>
                    <div id="cerrarSecion"></div>
                </li>
            </ul>
        </div>
    </li>
</ul>