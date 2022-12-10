<?php

declare(strict_types=1);

namespace Chronhub\Stream\Tests\Unit;

use Generator;
use Chronhub\Stream\GenericStream;
use Illuminate\Support\Collection;
use Chronhub\Stream\GenericStreamName;
use Illuminate\Support\LazyCollection;
use Chronhub\Stream\Tests\UnitTestCase;
use Chronhub\Stream\Tests\Double\SomeEvent;

final class GenericStreamTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider provideIterableEvents
     */
    public function it_instantiate_stream(iterable $events): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $stream = new GenericStream($streamName, $events);

        $this->assertSame($streamName, $stream->name());

        $this->assertInstanceOf(SomeEvent::class, $stream->events()->current());
    }

    /**
     * @test
     */
    public function it_instantiate_stream_with_generator(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $events = $this->provideGenerator();
        $stream = new GenericStream($streamName, $events);

        $this->assertSame($streamName, $stream->name());
        $this->assertInstanceOf(SomeEvent::class, $stream->events()->current());
    }

    public function provideIterableEvents(): Generator
    {
        yield[$this->dummyEvent()];

        yield [new Collection($this->dummyEvent())];

        yield [new LazyCollection($this->dummyEvent())];
    }

    private function provideGenerator(): Generator
    {
        yield from $this->dummyEvent();

        return 1;
    }

    private function dummyEvent(): array
    {
        return [SomeEvent::fromContent(['foo' => 'bar'])];
    }
}
