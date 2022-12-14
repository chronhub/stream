<?php

declare(strict_types=1);

namespace Chronhub\Stream\Tests\Unit;

use InvalidArgumentException;
use Chronhub\Testing\UnitTest;
use Chronhub\Stream\GenericStreamName;

final class GenericStreamNameTest extends UnitTest
{
    /**
     * @test
     */
    public function it_instantiate_with_name(): void
    {
        $streamName = new GenericStreamName('some_stream_name');

        $this->assertEquals($streamName, $streamName->name);
        $this->assertEquals($streamName, $streamName->__toString());
        $this->assertEquals($streamName, (string) $streamName);
    }

    /**
     * @test
     */
    public function it_raise_exception_when_name_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Stream name given can not be empty');

        new GenericStreamName('');
    }
}
