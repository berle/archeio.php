<?php

namespace Berle\Archeio;

trait QueryDispatchTrait
{
    
    public function query(QueryInterface $query, string $method, array $args = [])
    {
        $full_method = "query" . ucFirst($method);
        
        if (method_exists($this, $full_method)) {
            return $this->{ $full_method }($query, $args);
        }
        
        throw new UnsupportedException("Query ($method) not supported by this source");
    }

    
}
