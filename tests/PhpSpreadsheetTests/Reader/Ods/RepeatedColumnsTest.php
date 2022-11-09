<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Ods;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\Ods;
use PHPUnit\Framework\TestCase;

class RepeatedColumnsTest extends TestCase
{
    /**
     * @var Ods
     */
    private $reader;

    protected function setUp(): void
    {
        $this->reader = new Ods();
    }

    public function testDefinedNames(): void
    {
        $this->reader->setReadFilter(
            new class () implements IReadFilter {
                public function readCell($columnAddress, $row, $worksheetName = ''): bool
                {
                    return in_array($columnAddress, ['A', 'C', 'E', 'G', 'J', 'K'], true);
                }
            }
        );
        $spreadsheet = $this->reader->load('tests/data/Reader/Ods/RepeatedCells.ods');
        $worksheet = $spreadsheet->getActiveSheet();

        self::assertEquals('TestA', $worksheet->getCell('A1')->getValue());
        self::assertNull($worksheet->getCell('C1')->getValue());
        self::assertEquals('TestE', $worksheet->getCell('E1')->getValue());
        self::assertEquals('TestG', $worksheet->getCell('G1')->getValue());
        self::assertEquals('A', $worksheet->getCell('J1')->getValue());
        self::assertEquals('TestK', $worksheet->getCell('K1')->getValue());
    }
}
