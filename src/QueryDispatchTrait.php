<?php

namespace Berle\Archeio;

trait QueryDispatchTrait
{
    
    public function query(QueryInterface $query, string $method, array $args = [])
    {
        $full_method = "query" . ucfirst($method);
        
        if (method_exists($this, $full_method)) {
            return $this->{ $full_method }($query, $args);
        }
        
        $class = get_class($this);
        
        throw new UnsupportedException("Query ($method) not supported by source ($class)");
    }

    
}
