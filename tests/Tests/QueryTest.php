<?php

namespace Berle\Archeio\Tests;

use Berle\Archeio\Query;
use Berle\Archeio\RepositoryInterface;
use Berle\Archeio\SourceInterface;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{

    protected $source, $repository, $query;
    
    protected function setUp()
    {
        $this->source = $this->createMock(SourceInterface::class);
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->repository->method('getSource')->willReturn($this->source);
        $this->query = new Query($this->repository, 'Mock');
    }
    
    public function testGetType()
    {
        $this->assertSame($this->query->getType(), 'Mock');
    }
    
    public function testFilter()
    {
        $this->assertSame($this->query->getFilter(), []);
        $this->query->filter([ 'foo' => 'bar' ]);
        $this->assertSame($this->query->getFilter(), [ 'foo' => 'bar' ]);
        $this->query->filter([ 'hello' => 'world' ]);
        $this->assertSame($this->query->getFilter(), [ 'foo' => 'bar', 'hello' => 'world' ]);
    }
    
    public function testPage()
    {
        $this->assertSame($this->query->getPage(), 1);
        $this->query->page(42);
        $this->assertSame($this->query->getPage(), 42);
    }
    
    public function testLimit()
    {
        $this->assertSame($this->query->getLimit(), null);
        $this->assertSame($this->query->hasLimit(), false);
        $this->query->limit(42);
        $this->assertSame($this->query->hasLimit(), true);
        $this->assertSame($this->query->getLimit(), 42);
    }
    
    public function testAllQuery()
    {
        $this->source
            ->expects($this->once())
            ->method('queryAll')
            ->with($this->identicalTo($this->query));

        $this->query->all();
    }
    
    public function testFirstQuery()
    {
        $this->source
            ->expects($this->once())
            ->method('queryFirst')
            ->with($this->identicalTo($this->query));

        $this->query->first();
    }
    
}
