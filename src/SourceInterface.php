<?php

namespace Berle\Archeio;

interface SourceInterface
{
    
    public function handles(): array;
    public function store(array $resources): void;
    public function remove(array $resources): void;
    public function getStoreDependencies(): array;
    public function getRemoveDependencies(): array;
    public function queryAll(QueryInterface $query);
    public function queryFirst(QueryInterface $query);
    
}
