<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Parallel;

use PhpOffice\PhpSpreadsheet\Parallel\Backend\ParallelTaskError;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ParallelTaskErrorTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $error = new ParallelTaskError('Something failed', 42);
        self::assertSame('Something failed', $error->getMessage());
        self::assertSame(42, $error->getCode());
    }

    public function testDefaultCode(): void
    {
        $error = new ParallelTaskError('Error without code');
        self::assertSame('Error without code', $error->getMessage());
        self::assertSame(0, $error->getCode());
    }

    public function testSerializable(): void
    {
        $error = new ParallelTaskError('Serialized error', 99);
        $serialized = serialize($error);
        $restored = unserialize($serialized);

        self::assertInstanceOf(ParallelTaskError::class, $restored);
        self::assertSame('Serialized error', $restored->getMessage());
        self::assertSame(99, $restored->getCode());
    }

    public function testFromThrowable(): void
    {
        $error = ParallelTaskError::fromThrowable(new RuntimeException('Original failure', 7));

        self::assertSame('Original failure', $error->getMessage());
        self::assertSame(7, $error->getCode());
        self::assertSame(RuntimeException::class, $error->getExceptionClass());
        self::assertNotSame('', $error->getTraceAsString());
    }

    public function testGetSummaryWithClassAndTrace(): void
    {
        $error = new ParallelTaskError('Boom', 0, RuntimeException::class, '#0 {main}');

        self::assertSame("[RuntimeException] Boom\n#0 {main}", $error->getSummary());
    }

    public function testGetSummaryWithoutClassAndTrace(): void
    {
        $error = new ParallelTaskError('Boom');

        self::assertSame('Boom', $error->getSummary());
    }
}
