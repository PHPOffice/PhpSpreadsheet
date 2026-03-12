<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Parallel;

use PhpOffice\PhpSpreadsheet\Parallel\Backend\ParallelTaskError;
use PHPUnit\Framework\TestCase;

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
}
