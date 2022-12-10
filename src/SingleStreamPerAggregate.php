<?php

declare(strict_types=1);

namespace Chronhub\Stream;

use Chronhub\Contracts\Stream\Stream;
use Chronhub\Contracts\Stream\StreamName;
use Chronhub\Contracts\Aggregate\Identity;
use Chronhub\Contracts\Message\DomainEvent;
use Chronhub\Contracts\Stream\StreamProducer;

final class SingleStreamPerAggregate implements StreamProducer
{
    public function __construct(private readonly StreamName $streamName)
    {
    }

    public function toStreamName(string $aggregateId): StreamName
    {
        return $this->streamName;
    }

    public function toStream(Identity $aggregateId, iterable $events = []): Stream
    {
        return new GenericStream($this->streamName, $events);
    }

    public function isFirstCommit(DomainEvent $firstEvent): bool
    {
        return false;
    }

    public function isAutoIncremented(): bool
    {
        return true;
    }
}
