<?php

namespace Berle\Archeio;

class Work implements WorkInterface
{
    
    protected $stored = [];
    protected $removed = [];

    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    
    public function store($resource): WorkInterface
    {
        $type = $this->assertValidResource($resource);
        
        $this->stored[ $type ][] = $resource;
        
        return $this;
    }
    
    public function remove($resource): WorkInterface
    {
        $type = $this->assertValidResource($resource);
        
        $this->removed[ $type ][] = $resource;
        
        return $this;
    }
    
    public function empty(): WorkInterface
    {
        $this->stored = [];
        $this->removed = [];
        
        return $this;
    }
    
    public function flush(): WorkInterface
    {
        $this->repository->flush($this);
        
        return $this;
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
