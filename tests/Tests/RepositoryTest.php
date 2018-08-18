<?php

namespace Berle\Archeio\Tests;

use Berle\Archeio\InvalidTypeException;
use Berle\Archeio\Query;
use Berle\Archeio\Repository;
use Berle\Archeio\SourceInterface;
use Berle\Archeio\Work;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    
    protected $repository, $source;
    
    public function setUp()
    {
        $this->repository = new Repository;
        $source = $this->source = $this->createMock(SourceInterface::class);
        $source->method('handles')->willReturn([ 'Mock' ]);
        $this->repository->register($source);
    }
    
    public function testQueryTypeException()
    {
        $this->expectException(InvalidTypeException::class);
        $this->repository->query('SomeInvalidType');
    }
    
    public function testQueryClass()
    {
        $this->assertSame(get_class($this->repository->query('Mock')), Query::class);
    }
    
    public function testWorkClass()
    {
        $this->assertSame(get_class($this->repository->work()), Work::class);
    }
    
}
