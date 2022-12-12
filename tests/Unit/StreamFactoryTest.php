<?php

declare(strict_types=1);

namespace Chronhub\Stream\Tests\Unit;

use Generator;
use Chronhub\Stream\GenericStream;
use Chronhub\Stream\StreamFactory;
use Chronhub\Stream\GenericStreamName;
use Chronhub\Stream\Tests\UnitTestCase;
use Chronhub\Contracts\Stream\StreamName;
use Chronhub\Stream\Tests\Double\SomeEvent;
use function iterator_to_array;

final class StreamFactoryTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider provideStreamName
     */
    public function it_make_stream(StreamName|string $streamName): void
    {
        $factory = new StreamFactory();

        $stream = $factory($streamName);

        $this->assertInstanceOf(GenericStream::class, $stream);
        $this->assertInstanceOf(GenericStreamName::class, $stream->name());
        $this->assertNotSame($streamName, $stream->name());
        $this->assertEquals('some_stream_name', $stream->name()->name());

        $streamEvent = iterator_to_array($stream->events());

        $this->assertEmpty($streamEvent);
    }

    /**
     * @test
     * @dataProvider provideStreamName
     */
    public function it_make_stream_with_stream_events(StreamName|string $streamName): void
    {
        $factory = new StreamFactory();

        $expectedStreamEvent = SomeEvent::fromContent(['foo' => 'bar'])->withHeader('some', 'header');

        $stream = $factory($streamName, [$expectedStreamEvent]);

        $this->assertInstanceOf(GenericStream::class, $stream);
        $this->assertInstanceOf(GenericStreamName::class, $stream->name());
        $this->assertNotSame($streamName, $stream->name());
        $this->assertEquals('some_stream_name', $stream->name()->name());

        $streamEvent = iterator_to_array($stream->events())[0];

        $this->assertEquals(['some' => 'header'], $streamEvent->headers());
        $this->assertEquals(['foo' => 'bar'], $streamEvent->toContent());

        $this->assertEquals($expectedStreamEvent, $streamEvent);
    }

    public function provideStreamName(): Generator
    {
        yield ['some_stream_name'];
        yield [new GenericStreamName('some_stream_name')];
    }
}
