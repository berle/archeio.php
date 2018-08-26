<?php

namespace Berle\Archeio;

interface QueryInterface
{

    public function __construct(RepositoryInterface $repository, string $type);
    
    public function filter(array $filter): QueryInterface;

    public function getType(): string;
    public function hasFilter(string $key): bool;
    public function getFilter(string $key);
    public function getFilters(): array;

    public function all(): CollectionInterface;
    public function page(int $page, int $size): CollectionInterface;
    public function each(\Closure $callback): void;
    public function first();
    public function count(): int;
    public function get($id);

}
