<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Gnumeric;

use PhpOffice\PhpSpreadsheet\Cell\AddressRange;
use PhpOffice\PhpSpreadsheet\Reader\Gnumeric;
use PHPUnit\Framework\TestCase;

/**
 * All tests must run in separate process due to ini_set.
 *
 * @runTestsInSeparateProcesses
 */
class Secf5w4Test extends TestCase
{
    public function testf5w4(): void
    {
        ini_set('memory_limit', '512M');
        $filename = 'tests/data/Reader/Gnumeric/secf5w4.gnumeric';
        $reader = new Gnumeric();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $row = AddressRange::MAX_ROW;
        self::assertTrue($sheet->rowDimensionExists($row));
        $rowDimension = $sheet->getRowDimension($row);
        self::assertEqualsWithDelta(12.0, $rowDimension->getRowHeight(), 1.0e-8);
        ++$row;
        self::assertFalse($sheet->rowDimensionExists($row));
        $spreadsheet->disconnectWorksheets();
    }
}
