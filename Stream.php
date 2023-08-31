<?php

declare(strict_types=1);

namespace Storm\Stream;

use Generator;

use function count;

final class Stream
{
    private StreamEvents $events;

    public function __construct(
        public readonly StreamName $name,
        iterable $events = []
    ) {
        $this->events = new StreamEvents($events);
    }

    public function name(): StreamName
    {
        return $this->name;
    }

    public function events(): Generator
    {
        yield from $this->events->getIterator();

        return count($this->events);
    }
}
