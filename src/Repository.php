<?php

namespace Berle\Archeio;

class Repository implements RepositoryInterface
{
    
    protected $sources = [];
    protected $store_dependencies;
    protected $remove_dependencies;

    public function register(SourceInterface $source): void
    {
        foreach ($source->handles() as $type) {
            $this->sources[ $type ] = $source;
        }

        unset($this->store_dependencies);
        unset($this->remove_dependencies);
    }
    
    public function query(string $type): QueryInterface
    {
        $this->assertValidType($type);
        
        $query_class = $this->getQueryClass();
        
        return new $query_class($this, $type);
    }

    public function work(): WorkInterface
    {
        $work_class = $this->getWorkClass();
        
        return new $work_class($this);
    }
    
    public function flush(WorkInterface $work): void
    {
        if ($work->hasWork()) {
            $this->wrapFlush(function () use ($work)
            {
                $this->flushStore($work);
    
                $this->flushRemove($work);
            });

            $work->empty();
        }
    }
    
    protected function flushStore(WorkInterface $work): void
    {
        $sorter = new GroupedStringSort();
        
        $dependency_map = $this->getStoreDependencies();
        
        foreach ($work->getStoreList() as $resource) {
            $class = get_class($resource);
            $sorter->add($resource, $class, $dependency_map[ $class ]);
        }
        
        $sorted = $sorter->sort();
        $groups = $sorter->getGroups();
        
        foreach ($groups as $group) {
            $this
                ->getSource($group->name)
                ->performStoreWork(array_slice($results, $group->position, $group->length));
        }
    }

    protected function getStoreDependencies()
    {
        if (isset($this->store_dependencies)) {
            return $this->store_dependencies;
        }

        $dependency_map = [];
        
        foreach ($this->sources as $type => $source) {
            $dependency_map[ $type ] = $source->getStoreDependencies();
        }
        
        return $this->store_dependencies = $dependency_map;
    }

    protected function flushRemove(WorkInterface $work): void
    {
        $sorter = new GroupedStringSort();
        
        $dependency_map = $this->getRemoveDependencies();
        
        foreach ($work->getRemoveList() as $resource) {
            $sorter->add($resource, $resource->class, $dependency_map[ $resource->class ]);
        }
        
        $sorted = $sorter->sort();
        $groups = $sorter->getGroups();
        
        foreach ($groups as $group) {
            $this
                ->getSource($group->name)
                ->performRemoveWork(array_slice($results, $group->position, $group->length));
        }
    }

    protected function getRemoveDependencies()
    {
        if (isset($this->remove_dependencies)) {
            return $this->remove_dependencies;
        }

        $dependency_map = [];
        
        foreach ($this->sources as $type => $source) {
            $dependency_map[ $type ] = $source->getRemoveDependencies();
        }
        
        return $this->remove_dependencies = $dependency_map;
    }
    
    protected function wrapFlush(WorkInterface $work, Closure $callback): void
    {
        $callback();
    }
    
    protected function assertValidType($type): void
    {
        if (! $this->hasSource($type)) {
            throw new InvalidTypeException();
        }
    }
    
    public function hasSource(string $type): bool
    {
        return array_key_exists($type, $this->sources);
    }
    
    public function getSource(string $type): SourceInterface
    {
        return $this->sources[ $type ];
    }

    protected function getWorkClass(): string
    {
        return Work::class;
    }
    
    protected function getQueryClass(): string
    {
        return Query::class;
    }

}
