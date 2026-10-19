<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PHPUnit\Framework\TestCase;

class Sec973cTest extends TestCase
{
    public function test937c(): void
    {
        $this->expectException(ReaderException::class);
        $this->expectExceptionMessage('BOM says UTF-8 but encoding says ISO-2022-JP');
        $reader = new XlsxReader();
        $reader->load('tests/data/Reader/XLSX/sec937c.xlsx');
    }
}
