<?php

declare(strict_types=1);

namespace Chronhub\Stream\Tests\Unit;

use Generator;
use Chronhub\Testing\UnitTest;
use Chronhub\Stream\GenericStream;
use Illuminate\Support\Collection;
use Chronhub\Stream\GenericStreamName;
use Chronhub\Testing\Double\SomeEvent;
use Illuminate\Support\LazyCollection;
use Chronhub\Testing\Double\AnotherEvent;

final class GenericStreamTest extends UnitTest
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

    /**
     * @test
     */
    public function it_create_new_instance(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $stream = new GenericStream($streamName, [SomeEvent::fromContent([])]);

        $newStream = $stream->newInstance('another_stream_name', [AnotherEvent::fromContent([])]);

        $this->assertNotSame($stream, $newStream);

        $this->assertEquals('another_stream_name', $newStream->name()->name());
        $this->assertInstanceOf(AnotherEvent::class, $newStream->events()->current());
    }

    /**
     * @test
     */
    public function it_create_new_instance_from_itself(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $stream = new GenericStream($streamName, [SomeEvent::fromContent([])]);

        $newStream = $stream->newInstance();

        $this->assertNotSame($stream, $newStream);

        $this->assertEquals('some_stream_name', $newStream->name()->name());
        $this->assertInstanceOf(SomeEvent::class, $newStream->events()->current());
    }

    /**
     * @test
     */
    public function it_create_new_instance_from_stream_name_instance(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $stream = new GenericStream($streamName, [SomeEvent::fromContent([])]);

        $newStream = $stream->newInstance(null, [AnotherEvent::fromContent([])]);

        $this->assertNotSame($stream, $newStream);

        $this->assertEquals('some_stream_name', $newStream->name()->name());
        $this->assertInstanceOf(AnotherEvent::class, $newStream->events()->current());
    }

    /**
     * @test
     */
    public function it_create_new_instance_from_stream_events_instance(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $stream = new GenericStream($streamName, [SomeEvent::fromContent([])]);

        $newStream = $stream->newInstance(null, [AnotherEvent::fromContent([])]);

        $this->assertNotSame($stream, $newStream);

        $this->assertEquals('some_stream_name', $newStream->name()->name());
        $this->assertInstanceOf(AnotherEvent::class, $newStream->events()->current());
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
