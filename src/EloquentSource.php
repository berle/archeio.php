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
    
    public function pageQuery(QueryInterface $query, int $page, int $size): array
    {
        $dbq = $this->buildDbQuery($query->getFilters());

        $dbq->take($this->calcPageLimit($query, $page, $size));
        $dbq->skip($this->calcPageOffset($query, $page, $size));
        
        return $this->collection($dbq->get()->all());
    }
    
    protected function calcPageOffset(QueryInterface $query, int $page, int $size): int
    {
        return $query->getOffset() + (($page - 1) * $size);
    }
    
    protected function calcPageLimit(QueryInterface $query, int $page, int $size): int
    {
        if (! $query->hasLimit()) {
            // We have no limit, therefor we can always use the full pagesize as limit.
            return $size;
        }
        
        $last_offset = $query->getOffset() + $query->getLimit();
        $page_offset = $this->calcPageOffset($query, $page, $size);
        
        if (($page_offset + $size) > $last_offset) {
            $new_size = $last_offset - $page_offset;
            
            if ($new_size > 0) {
                return $new_size;
            } else {
                return 0;
            }
        }
        
        return $size;
    }

    public function eachQuery(QueryInterface $query, \Closure $callback): void
    {
        $page = 1;
        $size = $this->iterator_batch_size;

        $dbq = $this->buildDbQuery($query->getFilters());

        do {
            $dbq->skip($this->calcPageOffset($query, $page, $size));
            $dbq->take($this->calcPageLimit($query, $page, $size));
            $results = $dbq->get();
            $results->each($callback);
        } while($results->count() > 0);
    }
    
    public function allQuery(QueryInterface $query): CollectionInterface
    {
        $dbq = $this->buildDbQuery($query->getFilters());

        if ($query->hasOffset()) {
            $dbq->skip($query->getOffset());
        }

        if ($query->hasLimit()) {
            $dbq->take($query->getLimit());
        }

        return $this->collection($dbq->get()->all());
    }
    
    public function countQuery(QueryInterface $query): int
    {
        $dbq = $this->buildDbQuery($query->getFilters());

        if ($query->hasOffset()) {
            $dbq->skip($query->getOffset());
        }

        if ($query->hasLimit()) {
            $dbq->take($query->getLimit());
        }

        return $dbq->count();
    }
    
    public function getQuery(QueryInterface $query, $id = null)
    {
        $dbq = $this->buildDbQuery($query->getFilters());
        
        if ($query->hasOffset()) {
            $dbq->skip($query->getOffset());
        }

        if ($query->hasLimit()) {
            $dbq->take($query->getLimit());
        } else {
            $dbq->take(1);
        }
        
        $model = $dbq->where($this->id_key, $id)->get()->first();

        if (is_null($model)) {
            throw new NotFoundException();
        }
        
        return $model;
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
