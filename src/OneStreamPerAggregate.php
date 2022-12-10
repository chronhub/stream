<?php

declare(strict_types=1);

namespace Chronhub\Stream;

use Chronhub\Contracts\Stream\Stream;
use Chronhub\Contracts\Stream\StreamName;
use Chronhub\Contracts\Aggregate\Identity;
use Chronhub\Contracts\Message\DomainEvent;
use Chronhub\Contracts\Message\EventHeader;
use Chronhub\Contracts\Stream\StreamProducer;

final class OneStreamPerAggregate implements StreamProducer
{
    public function __construct(private readonly StreamName $streamName)
    {
    }

    public function toStreamName(string $aggregateId): StreamName
    {
        return new GenericStreamName($this->streamName->name.'-'.$aggregateId);
    }

    public function toStream(Identity $aggregateId, iterable $events = []): Stream
    {
        $streamName = $this->toStreamName((string) $aggregateId);

        return new GenericStream($streamName, $events);
    }

    public function isFirstCommit(DomainEvent $firstEvent): bool
    {
        return $firstEvent->header(EventHeader::AGGREGATE_VERSION) === 1;
    }

    public function isAutoIncremented(): bool
    {
        return false;
    }
}
