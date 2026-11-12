<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Sec9vwjTest extends TestCase
{
    public function test9vwj(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('bad sqref in ignoredErrors');
        $reader = new XlsxReader();
        $reader->load('tests/data/Reader/XLSX/sec9vwj.1.xlsx');
    }
}
