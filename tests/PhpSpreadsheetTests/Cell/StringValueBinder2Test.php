<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Cell;

use DateTime;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\IValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
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
        $richText = new RichText();
        $richText->createTextRun('6');
        $richText2 = new RichText();
        $richText2->createTextRun('a');
        $sheet->fromArray([
            [1, 'x', 3.2],
            ['y', -5, 'z'],
            [new DateTime(), $richText, $richText2],
            [new StringableObject('a'), new StringableObject(2), 'z'],
        ]);
        $ignoredCells = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coordinate = $cell->getCoordinate();
                $dataType = $cell->getDataType();
                if ($dataType !== DataType::TYPE_INLINE) {
                    self::assertSame(DataType::TYPE_STRING, $dataType, "not string for cell $coordinate");
                }
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
        $richText = new RichText();
        $richText->createTextRun('6');
        $richText2 = new RichText();
        $richText2->createTextRun('a');
        $sheet->fromArray([
            [1, 'x', 3.2],
            ['y', -5, 'z'],
            [new DateTime(), $richText, $richText2],
            [new StringableObject('a'), new StringableObject(2), 'z'],
        ]);
        $ignoredCells = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coordinate = $cell->getCoordinate();
                $dataType = $cell->getDataType();
                if ($dataType !== DataType::TYPE_INLINE) {
                    self::assertSame(DataType::TYPE_STRING, $dataType, "not string for cell $coordinate");
                }
                if ($cell->getIgnoredErrors()->getNumberStoredAsText()) {
                    $ignoredCells[] = $coordinate;
                }
            }
        }
        self::assertSame(['A1', 'C1', 'B2', 'B3', 'B4'], $ignoredCells);
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
        $richText = new RichText();
        $richText->createTextRun('6');
        $richText2 = new RichText();
        $richText2->createTextRun('a');
        $sheet->fromArray([
            [1, 'x', 3.2],
            ['y', -5, 'z'],
            [new DateTime(), $richText, $richText2],
            [new StringableObject('a'), new StringableObject(2), 'z'],
        ]);
        $ignoredCells = [];
        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $coordinate = $cell->getCoordinate();
                $expected = (is_int($cell->getValue()) || is_float($cell->getValue())) ? DataType::TYPE_NUMERIC : (($cell->getValue() instanceof RichText) ? DataType::TYPE_INLINE : DataType::TYPE_STRING);
                self::assertSame($expected, $cell->getDataType(), "wrong type for cell $coordinate");
                if ($cell->getIgnoredErrors()->getNumberStoredAsText()) {
                    $ignoredCells[] = $coordinate;
                }
            }
        }
        self::assertSame(['B3', 'B4'], $ignoredCells);
        $spreadsheet->disconnectWorksheets();
    }
}
