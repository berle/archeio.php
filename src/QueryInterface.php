<?php

namespace Berle\Archeio;

interface QueryInterface
{

    public function __construct(RepositoryInterface $repository, string $type);
    
    public function filter(array $filter): QueryInterface;
    public function page(int $page): QueryInterface;
    public function limit(int $limit): QueryInterface;
    
    public function first();
    public function all();
    
}
