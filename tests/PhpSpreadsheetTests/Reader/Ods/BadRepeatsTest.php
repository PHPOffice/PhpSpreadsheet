<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class BadRepeatsTest extends TestCase
{
    public function testBadRepeatedColsRead(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid number-columns-repeated');
        $reader = new Ods();
        $infile = 'tests/data/Reader/Ods/BadRepeatCol.ods';
        $reader->load($infile);
    }

    public function testBadRepeatedColsWorksheetInfo(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid number-columns-repeated');
        $reader = new Ods();
        $infile = 'tests/data/Reader/Ods/BadRepeatCol.ods';
        $reader->listWorksheetInfo($infile);
    }

    public function testBadRepeatedRowsRead(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid number-rows-repeated');
        $reader = new Ods();
        $infile = 'tests/data/Reader/Ods/BadRepeatRow.ods';
        $reader->load($infile);
    }

    public function testBadRepeatedRowsWorksheetInfo(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('Invalid number-rows-repeated');
        $reader = new Ods();
        $infile = 'tests/data/Reader/Ods/BadRepeatRow.ods';
        $reader->listWorksheetInfo($infile);
    }
}
