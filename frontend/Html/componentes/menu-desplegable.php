<?php
// This component expects $title and $items as arguments from plantilla()
// $items should be an array of associative arrays, e.g.,
// [
//   ['label' => 'Nuevo Ingreso', 'hx_post' => '/path/to/file.php', 'onclick_title' => 'Nuevo Ingreso'],
//   ['label' => 'Promovidos', 'hx_post' => '/path/to/otherfile.php', 'onclick_title' => 'Promovidos']
// ]
?>
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?= htmlspecialchars($title) ?>
    </button>
    <ul class="dropdown-menu "> <?php foreach ($items as $item): ?>
            <li>
                <a class="dropdown-item" href="#"
                   <?php if (isset($item['hx_post'])): ?> hx-post="<?= htmlspecialchars($item['hx_post']) ?>" <?php endif; ?>
                   <?php if (isset($item['hx_get'])): ?> hx-get="<?= htmlspecialchars($item['hx_get']) ?>" <?php endif; ?>
                   <?php if (isset($item['hx_target'])): ?> hx-target="<?= htmlspecialchars($item['hx_target']) ?>" <?php endif; ?>
                   <?php if (isset($item['hx_swap'])): ?> hx-swap="<?= htmlspecialchars($item['hx_swap']) ?>" <?php endif; ?>
                   <?php if (isset($item['hx_trigger'])): ?> hx-trigger="<?= htmlspecialchars($item['hx_trigger']) ?>" <?php endif; ?>
                   <?php if (isset($item['onclick_title'])): ?> onclick="cambiarTitulo(['<?= htmlspecialchars($item['onclick_title']) ?>'])" <?php endif; ?>
                >
                    <?= htmlspecialchars($item['label']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>