<?php

namespace Berle\Archeio\Tests;

use Berle\Archeio\InvalidResourceException;
use Berle\Archeio\RepositoryInterface;
use Berle\Archeio\Work;
use PHPUnit\Framework\TestCase;

class WorkTest extends TestCase
{
    
    protected $repository, $work;
    
    protected function setUp()
    {
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->work = new Work($this->repository);
    }
    
    public function testStoreException()
    {
        $this->expectException(InvalidResourceException::class);
        $this->populateStore();
    }
    
    public function testStore()
    {
        $this->alwaysValid();
        $stored = $this->populateStore();
        $this->assertSame($this->work->getStored(), [ 'stdClass' => [ $stored ] ]);
    }

    public function testRemoveException()
    {
        $this->expectException(InvalidResourceException::class);
        $this->populateRemove();
    }

    public function testRemove()
    {
        $this->alwaysValid();
        $removed = $this->populateRemove();
        $this->assertSame($this->work->getRemoved(), [ 'stdClass' => [ $removed ] ]);
    }
    
    public function testEmpty()
    {
        $this->alwaysValid();
        $stored = $this->populateStore();
        $removed = $this->populateRemove();
        $this->assertSame($this->work->getStored(), [ 'stdClass' => [ $stored ] ]);
        $this->assertSame($this->work->getRemoved(), [ 'stdClass' => [ $removed ] ]);
        $this->work->empty();
        $this->assertSame($this->work->getStored(), []);
        $this->assertSame($this->work->getRemoved(), []);
    }
    
    public function testFlush()
    {
        $this->repository
            ->expects($this->once())
            ->method('flush')
            ->with($this->identicalTo($this->work));
        
        $this->work->flush();
    }
    
    protected function alwaysValid()
    {
        $this->repository->method('hasSource')->willReturn(true);
    }
    
    protected function populateStore()
    {
        $object = new \stdClass;
        $this->work->store($object);

        return $object;
    }

    protected function populateRemove()
    {
        $object = new \stdClass;
        $this->work->remove($object);

        return $object;
    }
    
}
