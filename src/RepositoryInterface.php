<?php

namespace Berle\Archeio;

interface RepositoryInterface
{
    
    public function register(SourceInterface $source): void;
    public function query(string $type): QueryInterface;
    public function work(): WorkInterface;

    public function hasSource(string $type): bool;
    public function getSource(string $type): SourceInterface;
    public function flush(WorkInterface $work): int;

}
