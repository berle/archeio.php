<?php

namespace Berle\Archeio;

abstract class AbstractSource implements SourceInterface
{

    protected $dependencies = [];

    abstract public function handles(): array;
    abstract public function store(array $resources): void;
    abstract public function remove(array $resources): void;
    
    abstract public function allQuery(QueryInterface $query): CollectionInterface;
    abstract public function getQuery(QueryInterface $query, $id);

    public function dependencies(): array
    {
        return [];
    }
    
    public function pageQuery(QueryInterface $query, int $page, int $size): CollectionInterface
    {
        $items = $this->allQuery($query);
        
        return array_slice($items, $size * ($page - 1), $size);
    }

    public function eachQuery(QueryInterface $query, \Closure $callback): void
    {
        $this->allQuery($query)->each($callback);
    }
    
    public function countQuery(QueryInterface $query): int
    {
        return $this->allQuery($query)->count();
    }

    protected function collection(array $items): CollectionInterface
    {
        return new Collection($data);
    }
    
}
