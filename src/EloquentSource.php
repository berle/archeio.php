<?php

namespace Berle\Archeio;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class EloquentSource implements SourceInterface
{
    
    protected $store_dependencies = [];
    protected $remove_dependencies = [];
    protected $filter = [];
    protected $searchable = [
        'id' => 'id',
    ];

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
    
    public function getStoreDependencies(): array
    {
        return $this->store_dependencies;
    }
    
    public function getRemoveDependencies(): array
    {
        return $this->remove_dependencies;
    }

    public function queryAll(QueryInterface $query)
    {
        $dbq = $this->buildDbQuery($query->getFilter());

        $page = 1;

        if ($limit = $query->getLimit()) {
            $total = $dbq->count();
            $page = $query->getPage();
            $dbq->take($limit);
            $dbq->skip(($page - 1) * $limit);
            $results = $dbq->get();
        } else {
            $results = $dbq->get();
            $limit = $total = $results->count();
        }

	    $limit = $limit ?: 1;

        return new LengthAwarePaginator($results, $total, $limit, $page);
    }
    
    public function queryFirst(QueryInterface $query)
    {
        return $this->buildDbQuery($query->getFilter())
            ->take(1)
            ->get()
            ->first();
    }
    
    protected function newDbQuery(): Builder
    {
        $model_class = $this->model();
        
        return (new $model_class)->newQuery();
    }
    
    protected function buildDbQuery(array $filter = []): Builder
    {
        $dbq = $this->newDbQuery();

        $filter = array_merge($this->filter, $filter);

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
