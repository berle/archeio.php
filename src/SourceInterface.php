<?php

namespace Berle\Archeio;

interface SourceInterface
{
    
    public function handles(): array;
    public function store(array $resources): void;
    public function remove(array $resources): void;
    public function query(QueryInterface $query, string $method, array $args = []);
    public function getStoreDependencies(): array;
    public function getRemoveDependencies(): array;

}
