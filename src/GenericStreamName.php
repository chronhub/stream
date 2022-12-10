<?php

declare(strict_types=1);

namespace Chronhub\Stream;

use InvalidArgumentException;
use Chronhub\Contracts\Stream\StreamName;

final class GenericStreamName implements StreamName
{
    public readonly string $name;

    public function __construct(string $name)
    {
        if ($name === '') {
            throw new InvalidArgumentException('Stream name given can not be empty');
        }

        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
