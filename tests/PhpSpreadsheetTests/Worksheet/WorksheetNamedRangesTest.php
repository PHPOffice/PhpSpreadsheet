<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class WorksheetNamedRangesTest extends TestCase
{
    protected function getSpreadsheet(): Spreadsheet
    {
        $reader = new Xlsx();

        return $reader->load('tests/data/Worksheet/namedRangeTest.xlsx');
    }

    public function testCellExists(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'GREETING';

        $worksheet = $spreadsheet->getActiveSheet();
        $cellExists = $worksheet->cellExists($namedCell);
        self::assertTrue($cellExists);
        $spreadsheet->disconnectWorksheets();
    }

    public function testCellExistsUtf8(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'Χαιρετισμός';

        $worksheet = $spreadsheet->getActiveSheet();
        $cellExists = $worksheet->cellExists($namedCell);
        self::assertTrue($cellExists);
        $spreadsheet->disconnectWorksheets();
    }

    public function testCellNotExists(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'GOODBYE';

        $worksheet = $spreadsheet->getActiveSheet();
        $cellExists = $worksheet->cellExists($namedCell);
        self::assertFalse($cellExists);
        $spreadsheet->disconnectWorksheets();
    }

    public function testCellExistsInvalidScope(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'Result';

        $worksheet = $spreadsheet->getActiveSheet();
        $cellExists = $worksheet->cellExists($namedCell);
        self::assertFalse($cellExists);
        $spreadsheet->disconnectWorksheets();
    }

    public function testCellExistsRange(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedRange = 'Range1';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cell coordinate string can not be a range of cells');

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->cellExists($namedRange);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCell(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'GREETING';

        $worksheet = $spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($namedCell);
        self::assertSame('Hello', $cell->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellUtf8(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'Χαιρετισμός';

        $worksheet = $spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($namedCell);
        self::assertSame('नमस्ते', $cell->getValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellNotExists(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'GOODBYE';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid cell coordinate {$namedCell}");

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell($namedCell);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellInvalidScope(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'Result';
        $ucNamedCell = strtoupper($namedCell);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid cell coordinate {$ucNamedCell}");

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell($namedCell);
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellLocalScoped(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'Result';

        $spreadsheet->setActiveSheetIndexByName('Sheet2');
        $worksheet = $spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($namedCell);
        self::assertSame(8, $cell->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellNamedFormula(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'Result';

        $spreadsheet->setActiveSheetIndexByName('Sheet2');
        $worksheet = $spreadsheet->getActiveSheet();
        $cell = $worksheet->getCell($namedCell);
        self::assertSame(8, $cell->getCalculatedValue());
        $spreadsheet->disconnectWorksheets();
    }

    public function testGetCellWithNamedRange(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedCell = 'Range1';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cell coordinate string can not be a range of cells');

        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->getCell($namedCell);
        $spreadsheet->disconnectWorksheets();
    }

    public function testNamedRangeToArray(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedRange = 'Range1';

        $worksheet = $spreadsheet->getActiveSheet();
        $rangeData = $worksheet->namedRangeToArray($namedRange);
        self::assertSame([['1', '2', '3']], $rangeData);
        $rangeData = $worksheet->namedRangeToArray($namedRange, null, true, false);
        self::assertSame([[1, 2, 3]], $rangeData);
        $spreadsheet->disconnectWorksheets();
    }

    public function testInvalidNamedRangeToArray(): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $namedRange = 'Range2';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Named Range {$namedRange} does not exist");

        $worksheet = $spreadsheet->getActiveSheet();
        $rangeData = $worksheet->namedRangeToArray($namedRange);
        self::assertSame([[1, 2, 3]], $rangeData);
        $spreadsheet->disconnectWorksheets();
    }
}
