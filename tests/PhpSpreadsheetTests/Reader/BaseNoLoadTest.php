<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader;

use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PHPUnit\Framework\TestCase;

class BaseNoLoadTest extends TestCase
{
    public function testBaseNoLoad(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Reader classes must implement their own loadSpreadsheetFromFile() method');
        $reader = new BaseNoLoad();
        $reader->load('unknown.file');
    }

    public function testBaseNoLoadInfo(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('Reader classes must implement their own listWorksheetInfo() method');
        $reader = new BaseNoLoad();
        $reader->listWorksheetInfo('unknown.file');
    }
}
