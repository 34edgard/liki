<?php
class FileCache {
    private $cacheDir;
    
    public function __construct($dir = 'cache/') {
        $this->cacheDir = $dir;
        if (!is_dir($dir)) mkdir($dir, 0755, true);
    }
    
    public function get($key, $expiration = 3600) {
        $file = $this->cacheDir . md5($key) . '.cache';
        
        if (!file_exists($file)) return false;
        
        if (time() - filemtime($file) > $expiration) {
            unlink($file);
            return false;
        }
        
        return unserialize(file_get_contents($file));
    }
    
    public function set($key, $data) {
        $file = $this->cacheDir . md5($key) . '.cache';
        return file_put_contents($file, serialize($data));
    }
}
