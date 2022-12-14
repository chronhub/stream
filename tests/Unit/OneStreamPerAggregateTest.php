<?php

declare(strict_types=1);

namespace Chronhub\Stream\Tests\Unit;

use Generator;
use Chronhub\Stream\GenericStream;
use Chronhub\Testing\ProphecyTest;
use Chronhub\Stream\GenericStreamName;
use Chronhub\Testing\Double\SomeEvent;
use Chronhub\Contracts\Aggregate\Identity;
use Chronhub\Stream\OneStreamPerAggregate;
use Chronhub\Contracts\Message\DomainEvent;
use Chronhub\Contracts\Message\EventHeader;

final class OneStreamPerAggregateTest extends ProphecyTest
{
    private string $aggregateId = 'b962d9cd-8e0a-4494-8f93-8b964ceda06b';

    /**
     * @test
     */
    public function it_determine_stream_name(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $streamProducer = new OneStreamPerAggregate($streamName);

        $this->assertEquals(
            new GenericStreamName('some_stream_name-'.$this->aggregateId),
            $streamProducer->toStreamName($this->aggregateId)
        );
    }

    /**
     * @test
     * @dataProvider provideEvents
     */
    public function it_produce_stream(iterable $events): void
    {
        $streamName = new GenericStreamName('some_stream_name');
        $aggregateId = $this->prophesize(Identity::class);
        $aggregateId->__toString()->willReturn($this->aggregateId)->shouldBeCalledOnce();

        $streamProducer = new OneStreamPerAggregate($streamName);

        $stream = new GenericStream(
            new GenericStreamName('some_stream_name-'.$this->aggregateId),
            $events
        );

        $this->assertNotSame($streamName, $stream->name());
        $this->assertEquals($stream, $streamProducer->toStream($aggregateId->reveal(), $events));
    }

    /**
     * @test
     * @dataProvider provideEventsForFirstCommit
     */
    public function it_determine_if_event_is_first_commit(DomainEvent $event, bool $isFirstCommit): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $streamProducer = new OneStreamPerAggregate($streamName);

        $this->assertEquals($isFirstCommit, $streamProducer->isFirstCommit($event));
    }

    /**
     * @test
     */
    public function it_check_if_is_one_stream_per_aggregate_strategy(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $streamProducer = new OneStreamPerAggregate($streamName);

        $this->assertFalse($streamProducer->isAutoIncremented());
    }

    public function provideEvents(): Generator
    {
        yield [[]];
        yield [[SomeEvent::fromContent(['steph' => 'bug'])]];
    }

    public function provideEventsForFirstCommit(): Generator
    {
        yield [
            SomeEvent::fromContent(['steph' => 'bug'])
                ->withHeader(EventHeader::AGGREGATE_VERSION, 1),
            true,
        ];

        yield [
            SomeEvent::fromContent(['steph' => 'bug'])
                ->withHeader(EventHeader::AGGREGATE_VERSION, 2),
            false,
        ];

        yield [
            SomeEvent::fromContent(['steph' => 'bug'])
                ->withHeader(EventHeader::AGGREGATE_VERSION, 20),
            false,
        ];
    }
}
