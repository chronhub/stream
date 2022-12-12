<?php

declare(strict_types=1);

namespace Chronhub\Stream;

use Generator;
use Illuminate\Support\Enumerable;
use Chronhub\Contracts\Stream\Stream;
use Chronhub\Contracts\Stream\StreamName;
use Chronhub\Contracts\Message\DomainEvent;
use function count;
use function is_string;

final class GenericStream implements Stream
{
    /**
     * @var iterable<DomainEvent>
     */
    private iterable $events;

    public function __construct(public readonly StreamName $streamName, iterable $events = [])
    {
        $this->events = $events instanceof Enumerable
            ? $events->getIterator()
            : $events;
    }

    public function name(): StreamName
    {
        return $this->streamName;
    }

    public function events(): Generator
    {
        yield from $this->events;

        return $this->events instanceof Generator
            ? (int) $this->events->getReturn() : count($this->events);
    }

    public function newInstance(StreamName|string|null $streamName = null, ?iterable $events = null): Stream
    {
        $nameOfStream = null;

        if ($streamName !== null) {
            $nameOfStream = is_string($streamName) ? new GenericStreamName($streamName) : $streamName;
        }

        return new self($nameOfStream instanceof StreamName ? $nameOfStream : $this->streamName, $events ?? $this->events);
    }
}
