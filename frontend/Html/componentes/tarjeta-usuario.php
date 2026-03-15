<div class="tarjeta">
    <img src="<?= $avatar ?? '/frontend/Img/Logo.jpg' ?>" alt="<?= $nombre ?>" data-bind="avatar">
    <h3 data-bind="nombre"><?= $nombre ?? 'pepe' ?></h3>
    <p data-bind="email"><?= $email ?? 'pepe@gmail.com' ?></p>
</div>

