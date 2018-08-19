<?php

namespace Berle\Archeio;

interface QueryInterface
{

    public function __construct(RepositoryInterface $repository, string $type);
    
    public function filter(array $filter): QueryInterface;
    public function page(int $page): QueryInterface;
    public function limit(int $limit): QueryInterface;

    public function getType(): string;
    public function hasFilter(string $key): bool;
    public function getFilter(string $key);
    public function getFilters(): array;
    public function getPage(): int;
    public function hasLimit(): bool;
    public function getLimit(): ?int;
    
}
