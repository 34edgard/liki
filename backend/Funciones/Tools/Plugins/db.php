<?php

use Liki\Database\MigrationRunner;
use Liki\Consola\db;
$migracio = new MigrationRunner();

return [
   'migracion-run'=>[$migracio,'run'],
   'migracion-rollback'=>[$migracio,'rollback'],
    'import'=>[db::class,'import'],
    'export'=>[db::class,'exportDatabase']
    ];


