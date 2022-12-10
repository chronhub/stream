<?php

declare(strict_types=1);

namespace Chronhub\Stream\Tests\Unit;

use Generator;
use Chronhub\Stream\GenericStream;
use Chronhub\Stream\GenericStreamName;
use Chronhub\Contracts\Aggregate\Identity;
use Chronhub\Contracts\Message\DomainEvent;
use Chronhub\Contracts\Message\EventHeader;
use Chronhub\Stream\Tests\Double\SomeEvent;
use Chronhub\Stream\Tests\ProphecyTestCase;
use Chronhub\Stream\SingleStreamPerAggregate;

final class SingleStreamPerAggregateTest extends ProphecyTestCase
{
    private string $aggregateId = 'b962d9cd-8e0a-4494-8f93-8b964ceda06b';

    /**
     * @test
     */
    public function it_determine_stream_name_without_aggregate_identity(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $streamProducer = new SingleStreamPerAggregate($streamName);

        $this->assertEquals(
            new GenericStreamName('some_stream_name'),
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
        $aggregateId->__toString()->shouldNotBeCalled();

        $streamProducer = new SingleStreamPerAggregate($streamName);

        $stream = new GenericStream(
            new GenericStreamName('some_stream_name'),
            $events
        );

        $this->assertNotSame($streamName, $stream->name());
        $this->assertEquals($stream, $streamProducer->toStream($aggregateId->reveal(), $events));
    }

    /**
     * @test
     * @dataProvider provideEventsForFirstCommit
     */
    public function it_always_return_false_to_determine_if_event_is_first_commit(DomainEvent $event): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $streamProducer = new SingleStreamPerAggregate($streamName);

        $this->assertFalse($streamProducer->isFirstCommit($event));
    }

    /**
     * @test
     */
    public function it_check_if_is_single_stream_per_aggregate_strategy(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $streamProducer = new SingleStreamPerAggregate($streamName);

        $this->assertTrue($streamProducer->isAutoIncremented());
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
        ];

        yield [
            SomeEvent::fromContent(['steph' => 'bug'])
                ->withHeader(EventHeader::AGGREGATE_VERSION, 2),
        ];

        yield [
            SomeEvent::fromContent(['steph' => 'bug'])
                ->withHeader(EventHeader::AGGREGATE_VERSION, 20),
        ];
    }
}
