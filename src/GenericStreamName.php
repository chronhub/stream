<?php

declare(strict_types=1);

namespace Chronhub\Stream;

use InvalidArgumentException;
use Chronhub\Contracts\Stream\StreamName;

final class GenericStreamName implements StreamName
{
    public function __construct(public readonly string $name)
    {
        if ($this->name === '') {
            throw new InvalidArgumentException('Stream name given can not be empty');
        }
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
