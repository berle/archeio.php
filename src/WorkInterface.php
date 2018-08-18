<?php

namespace Berle\Archeio;

interface WorkInterface
{
    
    public function __construct(RepositoryInterface $repository);

    public function store($resource): WorkInterface;
    public function remove($resource): WorkInterface;
    public function flush(): WorkInterface;
    public function empty(): WorkInterface;
    
    public function hasWork(): bool;
    public function getStoreList(): array;
    public function getRemoveList(): array;

}
