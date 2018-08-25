<?php

namespace Berle\Archeio;

interface SourceInterface
{
    
    public function handles(): array;
    public function dependencies(): array;
    public function store(array $resources): void;
    public function remove(array $resources): void;

    public function pageQuery(QueryInterface $query, int $page, int $size);
    public function eachQuery(QueryInterface $query, \Closure $callback);
    public function allQuery(QueryInterface $query);
    public function getQuery(QueryInterface $query, $id);
}
