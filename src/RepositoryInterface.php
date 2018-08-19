<?php

namespace Berle\Archeio;

interface RepositoryInterface
{
    
    // consumer use
    public function register(SourceInterface $source): void;
    public function query(string $type): QueryInterface;
    public function work(): WorkInterface;

    // internal use
    public function hasSource(string $type): bool;
    public function getSource(string $type): SourceInterface;
    public function flush(WorkInterface $work): int;

}
