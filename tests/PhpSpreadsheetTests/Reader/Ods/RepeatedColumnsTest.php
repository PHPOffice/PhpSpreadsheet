<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class RepeatedColumnsTest extends TestCase
{
    public function testDefinedNames(): void
    {
        $reader = new Ods();
        $reader->setReadFilter(
            new class () implements IReadFilter {
                public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
                {
                    return in_array($columnAddress, ['A', 'C', 'E', 'G', 'J', 'K'], true);
                }
            }
        );
        $spreadsheet = $reader->load('tests/data/Reader/Ods/RepeatedCells.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        self::assertEquals('TestA', $worksheet->getCell('A1')->getValue());
        self::assertNull($worksheet->getCell('C1')->getValue());
        self::assertEquals('TestE', $worksheet->getCell('E1')->getValue());
        self::assertEquals('TestG', $worksheet->getCell('G1')->getValue());
        self::assertEquals('A', $worksheet->getCell('J1')->getValue());
        self::assertEquals('TestK', $worksheet->getCell('K1')->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    /**
     * Test that read filter correctly handles cells with number-columns-repeated
     * containing data, where some repeated columns pass the filter and others don't.
     *
     * @see https://github.com/PHPOffice/PhpSpreadsheet/issues/4802
     */
    public function testReadFilterWithRepeatedDataCells(): void
    {
        // The ODS file has: A=TestA, B-D=empty(repeated=3), E=TestE, F=empty,
        // G=TestG, H-J=SameValue(repeated=3), K=TestK
        $reader = new Ods();
        $reader->setReadFilter(
            new class () implements IReadFilter {
                public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
                {
                    return in_array($columnAddress, ['I', 'K'], true);
                }
            }
        );
        $spreadsheet = $reader->load('tests/data/Reader/Ods/RepeatedDataCells.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        // I1 is part of a repeated cell group (H-J) with value "SameValue"
        // The filter should correctly read I1 even though H (first in group) is not in the filter
        self::assertEquals('SameValue', $worksheet->getCell('I1')->getValue());
        self::assertEquals('TestK', $worksheet->getCell('K1')->getValue());

        // Columns not in filter should not have data
        self::assertNull($worksheet->getCell('A1')->getValue());
        self::assertNull($worksheet->getCell('H1')->getValue());
        self::assertNull($worksheet->getCell('J1')->getValue());

        $spreadsheet->disconnectWorksheets();
    }
}
