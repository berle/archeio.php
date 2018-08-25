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
        $this->assertSame($this->query->getFilters(), []);
        $this->query->filter([ 'foo' => 'bar' ]);
        $this->assertTrue($this->query->hasFilter('foo'));
        $this->assertSame($this->query->getFilter('foo'), 'bar');
        $this->assertSame($this->query->getFilters(), [ 'foo' => 'bar' ]);
        $this->query->filter([ 'hello' => 'world' ]);
        $this->assertSame($this->query->getFilters(), [ 'foo' => 'bar', 'hello' => 'world' ]);
    }
    
    public function testOffset()
    {
        $this->assertSame($this->query->getOffset(), 0);
        $this->assertSame($this->query->hasOffset(), false);
        $this->query->offset(42);
        $this->assertSame($this->query->hasOffset(), true);
        $this->assertSame($this->query->getOffset(), 42);
    }
    
    public function testLimit()
    {
        $this->assertSame($this->query->getLimit(), 1);
        $this->assertSame($this->query->hasLimit(), false);
        $this->query->limit(42);
        $this->assertSame($this->query->hasLimit(), true);
        $this->assertSame($this->query->getLimit(), 42);
    }
    
    public function testPageQuery()
    {
        $this->source
            ->expects($this->once())
            ->method('pageQuery')
            ->with($this->identicalTo($this->query), 42, 84);

        $this->query->page(42, 84);
    }

    public function testAllQuery()
    {
        $this->source
            ->expects($this->once())
            ->method('allQuery')
            ->with($this->identicalTo($this->query));

        $this->query->all();
    }
    
    public function testEachQuery()
    {
        $callback = function () {};
        
        $this->source
            ->expects($this->once())
            ->method('eachQuery')
            ->with($this->identicalTo($this->query), $this->identicalTo($callback));

        $this->query->each($callback);
    }

    public function testGetQuery()
    {
        $this->source
            ->expects($this->once())
            ->method('getQuery')
            ->with($this->identicalTo($this->query), 42);

        $this->query->get(42);
    }

}
