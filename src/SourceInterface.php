<?php

namespace Berle\Archeio;

interface SourceInterface
{
    
    public function handles(): array;
    public function dependencies(): array;
    public function store(array $resources): void;
    public function remove(array $resources): void;

    public function pageQuery(QueryInterface $query, int $page, int $size): array;
    public function eachQuery(QueryInterface $query, \Closure $callback): void;
    public function allQuery(QueryInterface $query): array;
    public function countQuery(QueryInterface $query): int;
    public function getQuery(QueryInterface $query, $id);
    
}
