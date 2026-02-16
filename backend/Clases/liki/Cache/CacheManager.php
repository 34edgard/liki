<?php

namespace Liki\Cache;



class CacheManager {
    private $memcached;
    
    public function __construct() {
        $this->memcached = new \Memcached();
        $this->memcached->addServer('localhost', 11211);
    }
    
    public function get($key) {
        return $this->memcached->get($key);
    }
    
    public function set($key, $data, $expiration = 3600) {
        return $this->memcached->set($key, $data, $expiration);
    }
    
    public function delete($key) {
        return $this->memcached->delete($key);
    }
}

