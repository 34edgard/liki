

<button class="btn btn-<?= $estilo ?>" 
<?php if(isset($hx)): ?>
   hx-target="<?= $hx['target'] ?>"
   hx-get="<?= $hx['url'] ?>"        
   hx-trigger="<?= $hx['trigger'] ?>"
<?php endif; ?>
  >
     <?= $contenido ?>
</button>

