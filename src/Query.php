<?php

namespace Berle\Archeio;

class Query implements QueryInterface
{
    
    protected $repository;
    protected $type;
    protected $filter = [];
    protected $page = 1;
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
    
    public function getFilter(): array
    {
        return $this->filter;
    }
    
    public function page(int $page): QueryInterface
    {
        if ($page >= 1) {
            $this->page = $page;
        }
        
        return $this;
    }
    
    public function getPage(): int
    {
        return $this->page;
    }
    
    public function limit(?int $limit): QueryInterface
    {
        if (is_null($limit)) {
            unset($this->limit);
        } elseif ($limit >= 1) {
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
    
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function __call($method, array $args)
    {
        return $this->getSource()->query($this, $method, $args);
    }
    
    protected function getSource()
    {
        return $this->repository->getSource($this->getType());
    }
    
}
