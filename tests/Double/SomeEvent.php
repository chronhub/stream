<?php

declare(strict_types=1);

namespace Chronhub\Stream\Tests\Double;

use Chronhub\Message\Domain;
use Chronhub\Contracts\Message\DomainEvent;
use Chronhub\Message\HasConstructableContent;

final class SomeEvent extends Domain implements DomainEvent
{
    use HasConstructableContent;
}
