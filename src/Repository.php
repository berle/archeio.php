<?php

namespace Berle\Archeio;

use MJS\TopSort\Implementations\StringSort;

class Repository implements RepositoryInterface
{
    
    protected $sources = [];
    protected $dependency_order;

    public function register(SourceInterface $source): void
    {
        foreach ($source->handles() as $type) {
            $this->sources[ $type ] = $source;
        }

        unset($this->dependency_order);
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
                $this->processResources(
                    $work->getStored(),
                    $this->getDependencyOrder(),
                    function (SourceInterface $source, array $resources)
                    {
                        $source->store($resources);
                    });
                
                $this->processResources(
                    $work->getRemoved(),
                    array_reverse($this->getDependencyOrder()),
                    function (SourceInterface $source, array $resources)
                    {
                        $source->remove($resources);
                    });
            });

            $work->empty();
        }
    }
    
    protected function processResources(array $resources, array $type_order, \Closure $callback)
    {
        foreach ($type_order as $type) {
            if (array_key_exists($type, $resources)) {
                $callback($this->getSource($type), $resources[ $type ]);
            }
        }
    }

    protected function getDependencyOrder(): array
    {
        if (isset($this->dependency_order)) {
            return $this->dependency_order;
        }
        
        $sorter = new StringSort();
        
        foreach ($this->sources as $type => $source) {
            $sorter->add($type, $source->dependencies());
        }
        
        return $this->dependency_order = $sorter->sort();
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
