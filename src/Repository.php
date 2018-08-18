<?php

namespace Berle\Archeio;

use MJS\TopSort\Implementations\StringSort;

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
        $resources = $work->getStored();
        
        foreach ($this->getStoreOrder() as $type) {
            if (array_key_exists($type, $resources)) {
                $this->getSource($type)->store($resources[ $type ]);
            }
        }
    }
    
    protected function getStoreOrder()
    {
        if (isset($this->store_order)) {
            return $this->store_order;
        }

        $sorter = new StringSort();
        
        foreach ($this->sources as $type => $source) {
            $sorter->add($type, $source->getStoreDependencies());
        }
        
        return $this->store_order = $sorter->sort();
    }

    protected function flushRemove(WorkInterface $work): void
    {
        $resources = $work->getRemoved();
        
        foreach ($this->getRemoveOrder() as $type) {
            if (array_key_exists($type, $resources)) {
                $this->getSource($type)->store($resources[ $type ]);
            }
        }
    }

    protected function getRemoveOrder()
    {
        if (isset($this->remove_order)) {
            return $this->remove_order;
        }

        $sorter = new StringSort();
        
        foreach ($this->sources as $type => $source) {
            $sorter->add($type, $source->getRemoveDependencies());
        }
        
        return $this->remove_order = $sorter->sort();
    }
    
    protected function wrapFlush(\Closure $callback): void
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
