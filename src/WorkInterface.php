<?php

namespace Berle\Archeio;

interface WorkInterface
{
    
    public function __construct(RepositoryInterface $repository);

    public function store($resource): WorkInterface;
    public function remove($resource): WorkInterface;
    public function autoflush(int $every): WorkInterface;
    public function flush(): int;
    public function empty(): int;
    
    public function hasWork(): bool;
    public function getStored(): array;
    public function getRemoved(): array;

}
