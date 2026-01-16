<?php

use Liki\Database\MigrationRunner;
use Liki\Consola\db;

return [
   'migracion-run'=>[MigrationRunner::class,'run'],
    'import'=>[db::class,'import'],
    'export'=>[db::class,'exportDatabase']
    ];
