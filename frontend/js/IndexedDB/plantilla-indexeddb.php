// frontend/js/IndexedDB/plantilla-indexeddb.php  
<?php  
use Liki\Plantillas\Flow;  
  
$nombreDB = $datos['nombre_db'] ?? 'MiDB';  
$version = $datos['version'] ?? 1;  
$stores = $datos['stores'] ?? [];  
$scripts = $datos['scripts'] ?? [];  
?>  
  
class <?= $nombreDB ?>DB {  
    constructor() {  
        this.dbName = '<?= $nombreDB ?>';  
        this.version = <?= $version ?>;  
        this.db = null;  
    }  
      
    async init() {  
        return new Promise((resolve, reject) => {  
            const request = indexedDB.open(this.dbName, this.version);  
              
            request.onupgradeneeded = (e) => {  
                const db = e.target.result;  
                <?php foreach ($stores as $store): ?>  
                    if (!db.objectStoreNames.contains('<?= $store['name'] ?>')) {  
                        const store = db.createObjectStore('<?= $store['name'] ?>',   
                            { keyPath: '<?= $store['keyPath'] ?>' });  
                    }  
                <?php endforeach; ?>  
            };  
              
            request.onsuccess = () => {  
                this.db = request.result;  
                resolve(this.db);  
            };  
              
            request.onerror = () => reject(request.error);  
        });  
    }  
      
    // Inyectar scripts de lógica CRUD  
    <?php foreach ($scripts as $script): ?>  
        <?php Flow::js($script, $datos); ?>  
    <?php endforeach; ?>  
}