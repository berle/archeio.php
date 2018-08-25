<?php

namespace Berle\Archeio;

interface QueryInterface
{

    public function __construct(RepositoryInterface $repository, string $type);
    
    public function filter(array $filter): QueryInterface;
    public function offset(int $offset): QueryInterface;
    public function limit(int $limit): QueryInterface;

    public function getType(): string;
    public function hasFilter(string $key): bool;
    public function getFilter(string $key);
    public function getFilters(): array;
    public function hasOffset(): bool;
    public function getOffset(): int;
    public function hasLimit(): bool;
    public function getLimit(): int;
    
    public function page(int $page, int $size): array;
    public function each(\Closure $callback): void;
    public function all(): array;
    public function count(): int;
    public function get($id);

}
