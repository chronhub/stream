<?php

declare(strict_types=1);

namespace Chronhub\Stream;

use Chronhub\Contracts\Stream\Stream;
use Chronhub\Contracts\Stream\Factory;
use Chronhub\Contracts\Stream\StreamName;

final class StreamFactory implements Factory
{
    public function __invoke(StreamName|string $streamName, iterable $streamEvents = []): Stream
    {
        $nameOfStream = $streamName instanceof StreamName ? $streamName->name() : $streamName;

        return new GenericStream(new GenericStreamName($nameOfStream), $streamEvents);
    }
}
