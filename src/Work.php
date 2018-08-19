<?php

namespace Berle\Archeio;

class Work implements WorkInterface
{
    
    protected $stored = [];
    protected $removed = [];
    protected $count = 0;

    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    
    public function store($resource): WorkInterface
    {
        $type = $this->assertValidResource($resource);
        
        $this->stored[ $type ][] = $resource;
        $this->count++;
        
        return $this;
    }
    
    public function remove($resource): WorkInterface
    {
        $type = $this->assertValidResource($resource);
        
        $this->removed[ $type ][] = $resource;
        $this->count++;
        
        return $this;
    }
    
    protected function count(): int
    {
        return $this->count;
    }
    
    public function empty(): int
    {
        $count = $this->count();
        $this->stored = [];
        $this->removed = [];
        $this->count = 0;
        
        return $count;
    }
    
    public function flush(): int
    {
        $count =  $this->count();
        $this->repository->flush($this);
        
        return $count;
    }
    
    protected function assertValidResource($resource): string
    {
        if (is_object($resource)) {
            $type = get_class($resource);
            
            if ($this->repository->hasSource($type)) {
                return $type;
            }
        }
        
        throw new InvalidResourceException();
    }
    
    public function hasWork(): bool
    {
        if (count($this->stored) > 0 || count($this->removed) > 0) {
            return true;
        }
        
        return false;
    }
    
    public function getStored(): array
    {
        return $this->stored;
    }
    
    public function getRemoved(): array
    {
        return $this->removed;
    }
    
}
