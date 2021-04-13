<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class WorksheetNamedRangesTest extends TestCase
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    protected function setUp(): void
    {
        Settings::setLibXmlLoaderOptions(null); // reset to default options

        $reader = new Xlsx();
        $this->spreadsheet = $reader->load('tests/data/Worksheet/namedRangeTest.xlsx');
    }

    public function testCellExists(): void
    {
        $namedCell = 'GREETING';

        $worksheet = $this->spreadsheet->getActiveSheet();
        $cellExists = $worksheet->cellExists($namedCell);
        self::assertTrue($cellExists);
    }

    public function testCellNotExists(): void
    {
        $namedCell = 'GOODBYE';

        $worksheet = $this->spreadsheet->getActiveSheet();
        $cellExists = $worksheet->cellExists($namedCell);
        self::assertFalse($cellExists);
    }

    public function testCellExistsInvalidScope(): void
    {
        $namedCell = 'Result';

        $worksheet = $this->spreadsheet->getActiveSheet();
        $cellExists = $worksheet->cellExists($namedCell);
        self::assertFalse($cellExists);
    }

    public function testCellExistsRange(): void
    {
        $namedRange = 'Range1';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cell coordinate string can not be a range of cells');

        $worksheet = $this->spreadsheet->getActiveSheet();
        $worksheet->cellExists($namedRange);
    }

    public function testGetCell(): void
    {
        $namedCell = 'GREETING';

        $worksheet = $this->spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($namedCell);
        self::assertSame('Hello', $cell->getValue());
    }

    public function testGetCellNotExists(): void
    {
        $namedCell = 'GOODBYE';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid cell coordinate {$namedCell}");

        $worksheet = $this->spreadsheet->getActiveSheet();
        $worksheet->getCell($namedCell);
    }

    public function testGetCellInvalidScope(): void
    {
        $namedCell = 'Result';
        $ucNamedCell = strtoupper($namedCell);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid cell coordinate {$ucNamedCell}");

        $worksheet = $this->spreadsheet->getActiveSheet();
        $worksheet->getCell($namedCell);
    }

    public function testGetCellLocalScoped(): void
    {
        $namedCell = 'Result';

        $this->spreadsheet->setActiveSheetIndexByName('Sheet2');
        $worksheet = $this->spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($namedCell);
        self::assertSame(8, $cell->getCalculatedValue());
    }

    public function testGetCellNamedFormula(): void
    {
        $namedCell = 'Result';

        $this->spreadsheet->setActiveSheetIndexByName('Sheet2');
        $worksheet = $this->spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($namedCell);
        self::assertSame(8, $cell->getCalculatedValue());
    }

    public function testGetCellWithNamedRange(): void
    {
        $namedCell = 'Range1';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cell coordinate string can not be a range of cells');

        $worksheet = $this->spreadsheet->getActiveSheet();
        $worksheet->getCell($namedCell);
    }

    public function testNamedRangeToArray(): void
    {
        $namedRange = 'Range1';

        $worksheet = $this->spreadsheet->getActiveSheet();
        $rangeData = $worksheet->namedRangeToArray($namedRange);
        self::assertSame([[1, 2, 3]], $rangeData);
    }

    public function testInvalidNamedRangeToArray(): void
    {
        $namedRange = 'Range2';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Named Range {$namedRange} does not exist");

        $worksheet = $this->spreadsheet->getActiveSheet();
        $rangeData = $worksheet->namedRangeToArray($namedRange);
        self::assertSame([[1, 2, 3]], $rangeData);
    }
}
