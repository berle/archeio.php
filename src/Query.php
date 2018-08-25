<?php

namespace Berle\Archeio;

class Query implements QueryInterface
{
    
    protected $repository;
    protected $type;
    protected $filter = [];
    protected $offset;
    protected $limit;

    public function __construct(
        RepositoryInterface $repository,
        string $type
    ) {
        $this->repository = $repository;
        $this->type = $type;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function filter(array $filter): QueryInterface
    {
        $this->filter = array_merge($this->filter, $filter);
        
        return $this;
    }
    
    public function hasFilter(string $key): bool
    {
        return array_key_exists($key, $this->filter);
    }
    
    public function getFilter(string $key)
    {
        if ($this->hasFilter($key)) {
            return $this->filter[ $key ];
        }
        
        return null;
    }
    
    public function getFilters(): array
    {
        return $this->filter;
    }
    
    public function offset(int $offset): QueryInterface
    {
        if ($offset >= 0) {
            $this->offset = $offset;
        }
        
        return $this;
    }
    
    public function hasOffset(): bool
    {
        if (isset($this->offset)) {
            return true;
        }
        
        return false;
    }
    
    public function getOffset(): int
    {
        return $this->offset ?: 0;
    }
    
    public function limit(int $limit): QueryInterface
    {
        if ($limit >= 1) {
            $this->limit = $limit;
        }
        
        return $this;
    }
    
    public function hasLimit(): bool
    {
        if (isset($this->limit)) {
            return true;
        }
        
        return false;
    }
    
    public function getLimit(): int
    {
        return $this->limit ?: 1;
    }

    public function page(int $page, int $size): CollectionInterface
    {
        return $this->getSource()->pageQuery($this, $page, $size);
    }
    
    public function each(\Closure $callback): void
    {
        $this->getSource()->eachQuery($this, $callback);
    }
    
    public function all(): CollectionInterface
    {
        return $this->getSource()->allQuery($this);
    }
    
    public function count(): int
    {
        return $this->getSource()->countQuery($this);
    }
    
    public function get($id)
    {
        return $this->getSource()->getQuery($this, $id);
    }
    
    protected function getSource()
    {
        return $this->repository->getSource($this->getType());
    }
    
}
