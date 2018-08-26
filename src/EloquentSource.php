<?php

namespace Berle\Archeio;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class EloquentSource extends AbstractSource
{

    protected $filter = [];
    protected $id_key = 'id';
    protected $searchable = [
        'id' => 'id',
    ];
    protected $iterator_batch_size = 500;

    abstract protected function model(): string;

    public function handles(): array
    {
        return [ $this->model() ];
    }
    
    public function store(array $resources): void
    {
        foreach ($resources as $resource) {
            $resource->save();
        }
    }
    
    public function remove(array $resources): void
    {
        foreach ($resources as $resource) {
            $resource->delete();
        }
    }
    
    public function pageQuery(QueryInterface $query, int $page, int $size): CollectionInterface
    {
        $dbq = $this->dbqFromQuery($query)
            ->skip(($page - 1) * $size)
            ->take($size);

        return $this->collection($dbq->get()->all());
    }

    public function eachQuery(QueryInterface $query, \Closure $callback): void
    {
        $dbq = $this->dbqFromQuery($query);
        
        $dbq->each($callback, $this->iterator_batch_size);
    }
    
    public function allQuery(QueryInterface $query): CollectionInterface
    {
        $dbq = $this->dbqFromQuery($query);

        return $this->collection($dbq->get()->all());
    }
    
    public function countQuery(QueryInterface $query): int
    {
        return $this->dbqFromQuery($query)
            ->count();
    }
    
    public function firstQuery(QueryInterface $query)
    {
        return $this->dbqFromQuery($query)
            ->take(1)
            ->get()
            ->first();
    }
    
    public function getQuery(QueryInterface $query, $id = null)
    {
        return $this->dbqFromQuery($query)
            ->where($this->id_key, $id)
            ->take(1)
            ->get()
            ->first();
    }

    protected function newDbq(): Builder
    {
        $model_class = $this->model();
        
        return (new $model_class)->newQuery();
    }
    
    protected function dbqFromQuery(QueryInterface $query): Builder
    {
        return $this->buildDbQuery($query->getFilters());
    }
    
    protected function buildDbQuery(array $filter): Builder
    {
        $dbq = $this->newDbq();

        $filter = array_merge($filter, $this->filter);

        foreach ($filter as $key => $value) {
            if ($spec = $this->getSearchable($key)) {
                if (array_key_exists('op', $spec)) {
                    $dbq->where($spec[ 'col' ], $spec[ 'op' ], $value);
                } else {
                    if (is_array($value)) {
                        $dbq->whereIn($spec[ 'col' ], $value);
                    } else {
                        $dbq->where($spec[ 'col' ], $value);
                    }
                }
            }
        }
        
        return $dbq;
    }

    protected function getSearchable(string $key): ?array
    {
        if (array_key_exists($key, $this->searchable)) {
            $spec = $this->searchable[ $key ];

            if (is_array($spec)) {
                return $spec;
            } else {
                return [
                    'col' => $spec,
                ];
            }
        }

        return null;
    }

}
