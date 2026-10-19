<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Worksheet\Table;

use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PHPUnit\Framework\Attributes\DataProvider;

class TotalsRowCellTest extends SetupTeardown
{
    private function createTable(bool $showTotalsRow = true): Table
    {
        $sheet = $this->getSheet();
        $sheet->fromArray([
            ['Name', 'Qty', 'Price [USD]'],
            ['a', 1, 2],
            ['b', 3, 6],
        ]);
        $table = new Table('A1:C4', 'Sales');
        $table->setShowTotalsRow($showTotalsRow);

        return $table;
    }

    #[DataProvider('providerTotalsRowFunction')]
    public function testTotalsRowFunction(string $function, int $subtotal): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable();
        $sheet->addTable($table);

        $table->getColumn('B')->setTotalsRowFunction($function);

        self::assertSame("=SUBTOTAL($subtotal,Sales[Qty])", $sheet->getCell('B4')->getValue());
    }

    public static function providerTotalsRowFunction(): array
    {
        return [
            ['average', 101],
            ['countNums', 102],
            ['count', 103],
            ['max', 104],
            ['min', 105],
            ['stdDev', 107],
            ['sum', 109],
            ['var', 110],
        ];
    }

    public function testSetBeforeAddTable(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable();
        $table->getColumn('A')->setTotalsRowLabel('Total');
        $table->getColumn('B')->setTotalsRowFunction('sum');
        $table->getColumn('C')->setTotalsRowFormula('=MAX(Sales[Qty])*2');
        self::assertFalse($sheet->cellExists('B4'));

        $sheet->addTable($table);

        self::assertSame('Total', $sheet->getCell('A4')->getValue());
        self::assertSame('=SUBTOTAL(109,Sales[Qty])', $sheet->getCell('B4')->getValue());
        self::assertSame('=MAX(Sales[Qty])*2', $sheet->getCell('C4')->getValue());
        self::assertSame(4, $sheet->getCell('B4')->getCalculatedValue());
    }

    public function testShowTotalsRowAfterAddTable(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable(false);
        $table->getColumn('B')->setTotalsRowFunction('sum');
        $sheet->addTable($table);
        self::assertFalse($sheet->cellExists('B4'));

        $table->setShowTotalsRow(true);

        self::assertSame('=SUBTOTAL(109,Sales[Qty])', $sheet->getCell('B4')->getValue());
    }

    public function testCustomFormulaWithoutEquals(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable();
        $sheet->addTable($table);

        $table->getColumn('B')->setTotalsRowFunction('custom')->setTotalsRowFormula('SUBTOTAL(109,Sales[Qty])*2');

        self::assertSame('=SUBTOTAL(109,Sales[Qty])*2', $sheet->getCell('B4')->getValue());
        self::assertSame(8, $sheet->getCell('B4')->getCalculatedValue());
    }

    public function testColumnNameIsEscaped(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable();
        $sheet->addTable($table);

        $table->getColumn('C')->setTotalsRowFunction('average');

        // Escaped as Excel does; the calculation engine does not handle the escapes yet
        self::assertSame("=SUBTOTAL(101,Sales[Price '[USD']])", $sheet->getCell('C4')->getValue());
    }

    public function testExistingValueIsKept(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable();
        $sheet->getCell('B4')->setValue('=SUBTOTAL(109,Sales[Qty])+1');
        $sheet->addTable($table);

        $table->getColumn('B')->setTotalsRowFunction('average');

        self::assertSame('=SUBTOTAL(109,Sales[Qty])+1', $sheet->getCell('B4')->getValue());
    }

    public function testNothingWithoutTotalsRowOrFunction(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable(false);
        $sheet->addTable($table);
        $table->getColumn('B')->setTotalsRowFunction('sum');
        self::assertFalse($sheet->cellExists('B4'));

        $table->setShowTotalsRow(true);
        $table->getColumn('C')->setTotalsRowFunction('none');
        self::assertFalse($sheet->cellExists('C4'));
    }

    public function testNoHeaderRow(): void
    {
        $sheet = $this->getSheet();
        $table = $this->createTable();
        $table->setShowHeaderRow(false);
        $sheet->addTable($table);

        $table->getColumn('B')->setTotalsRowFunction('sum');

        self::assertSame('=SUBTOTAL(109,Sales[Column2])', $sheet->getCell('B4')->getValue());
    }

    public function testReaderDoesNotFillSkippedTotalsRow(): void
    {
        $reader = new XlsxReader();
        $reader->setReadFilter(new class () implements IReadFilter {
            public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
            {
                return $row < 8;
            }
        });
        $spreadsheet = $reader->load('tests/data/Reader/XLSX/TableWithoutFilter.xlsx');
        $sheet = $spreadsheet->getActiveSheet();

        self::assertCount(1, $sheet->getTableCollection());
        self::assertFalse($sheet->cellExists('C8'));
        self::assertFalse($sheet->cellExists('E8'));
        $spreadsheet->disconnectWorksheets();
    }
}
