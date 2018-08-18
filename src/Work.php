<?php

namespace Berle\Archeio;

class Work implements WorkInterface
{
    
    protected $store_list = [];
    protected $remove_list = [];

    public function __construct(
        RepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    
    public function store($resource): WorkInterface
    {
        $this->assertValidResource($resource);
        
        $this->store_list[] = $resource;
        
        return $this;
    }
    
    public function remove($resource): WorkInterface
    {
        $this->assertValidResource($resource);
        
        $this->remove_list[] = $resource;
        
        return $this;
    }
    
    public function empty(): WorkInterface
    {
        $this->store_list = [];
        $this->remove_list = [];
        
        return $this;
    }
    
    public function flush(): WorkInterface
    {
        $this->repository->flush($this);
        
        return $this;
    }
    
    protected function assertValidResource($resource)
    {
        if (is_object($resource) && $this->repository->hasSource(get_class($resource))) {
            return true;
        }
        
        throw new InvalidResourceException();
    }
    
    public function hasWork(): bool
    {
        if (count($this->store_list) > 0 || count($this->remove_list) > 0) {
            return true;
        }
        
        return false;
    }
    
    public function getStoreList(): array
    {
        return $this->store_list;
    }
    
    public function getRemoveList(): array
    {
        return $this->remove_list;
    }
    
}
