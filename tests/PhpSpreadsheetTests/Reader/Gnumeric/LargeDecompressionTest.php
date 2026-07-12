<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PHPUnit\Framework\TestCase;

class LargeDecompressionTest extends TestCase
{
    private const FILENAME = 'tests/data/Reader/Gnumeric/gzbomb.gnumeric';

    public function testEnoughMemoryCanRead(): void
    {
        $reader = new Gnumeric();
        self::assertTrue($reader->canRead(self::FILENAME));
    }

    public function testNotEnoughMemoryCanRead(): void
    {
        $reader = new Gnumeric();
        $reader->setMaxLength(64 * 1024 * 1024);
        self::assertFalse($reader->canRead(self::FILENAME));
    }

    public function testNotEnoughMemoryListNames(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('invalid Gnumeric file');
        $reader = new Gnumeric();
        $reader->setMaxLength(64 * 1024 * 1024);
        $reader->listWorksheetNames(self::FILENAME);
    }

    public function testNotEnoughMemoryListInfo(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('invalid Gnumeric file');
        $reader = new Gnumeric();
        $reader->setMaxLength(64 * 1024 * 1024);
        $reader->listWorksheetInfo(self::FILENAME);
    }

    public function testNotEnoughMemoryLoad(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('invalid Gnumeric file');
        $reader = new Gnumeric();
        $reader->setMaxLength(64 * 1024 * 1024);
        $reader->load(self::FILENAME);
    }
}
