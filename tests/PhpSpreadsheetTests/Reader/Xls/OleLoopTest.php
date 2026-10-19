<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PHPUnit\Framework\TestCase;

class OleLoopTest extends TestCase
{
    private const FILENAME = 'tests/data/Reader/XLS/oleloop.xls';

    public function testDetectLoopCanRead(): void
    {
        $reader = new XlsReader();
        self::assertFalse($reader->canRead(self::FILENAME));
    }

    public function testDetectLoopListNames(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Detected loop');
        $reader = new XlsReader();
        $reader->listWorksheetNames(self::FILENAME);
    }

    public function testDetectLoopListInfo(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Detected loop');
        $reader = new XlsReader();
        $reader->listWorksheetInfo(self::FILENAME);
    }

    public function testDetectLoopLoad(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Detected loop');
        $reader = new XlsReader();
        $reader->load(self::FILENAME);
    }
}
