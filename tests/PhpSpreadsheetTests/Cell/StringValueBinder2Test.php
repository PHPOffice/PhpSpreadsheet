<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class StringValueBinder2Test extends TestCase
{
    private IValueBinder $valueBinder;

    protected function setUp(): void
    {
        $this->valueBinder = Cell::getValueBinder();
    }

    protected function tearDown(): void
    {
        Cell::setValueBinder($this->valueBinder);
    }

    public function testStringValueBinderIgnoredErrorsDefault(): void
    {
        $valueBinder = new StringValueBinder();
        Cell::setValueBinder($valueBinder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 'x', 3.2],
            ['y', -5, 'z'],
        ]);
        $ignoredCells = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coordinate = $cell->getCoordinate();
                self::assertSame(DataType::TYPE_STRING, $cell->getDataType(), "not string for cell $coordinate");
                if ($cell->getIgnoredErrors()->getNumberStoredAsText()) {
                    $ignoredCells[] = $coordinate;
                }
            }
        }
        self::assertSame([], $ignoredCells);
        $spreadsheet->disconnectWorksheets();
    }

    public function testStringValueBinderIgnoredErrorsTrue(): void
    {
        $valueBinder = new StringValueBinder();
        $valueBinder->setSetIgnoredErrors(true);
        Cell::setValueBinder($valueBinder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 'x', 3.2],
            ['y', -5, 'z'],
        ]);
        $ignoredCells = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coordinate = $cell->getCoordinate();
                self::assertSame(DataType::TYPE_STRING, $cell->getDataType(), "not string for cell $coordinate");
                if ($cell->getIgnoredErrors()->getNumberStoredAsText()) {
                    $ignoredCells[] = $coordinate;
                }
            }
        }
        self::assertSame(['A1', 'C1', 'B2'], $ignoredCells);
        $spreadsheet->disconnectWorksheets();
    }

    public function testStringValueBinderPreserveNumeric(): void
    {
        $valueBinder = new StringValueBinder();
        $valueBinder->setNumericConversion(false);
        $valueBinder->setSetIgnoredErrors(true);
        Cell::setValueBinder($valueBinder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            [1, 'x', 3.2],
            ['y', -5, 'z'],
        ]);
        $ignoredCells = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coordinate = $cell->getCoordinate();
                $expected = is_numeric($cell->getValue()) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING;
                self::assertSame($expected, $cell->getDataType(), "wrong type for cell $coordinate");
                if ($cell->getIgnoredErrors()->getNumberStoredAsText()) {
                    $ignoredCells[] = $coordinate;
                }
            }
        }
        self::assertSame([], $ignoredCells);
        $spreadsheet->disconnectWorksheets();
    }
}
