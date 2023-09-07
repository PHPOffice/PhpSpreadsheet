<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\Operands\StructuredReference;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PHPUnit\Framework\TestCase;

class StructuredReferenceSlashTest extends TestCase
{
    protected ?Spreadsheet $spreadSheet;

    protected const COLUMN_FORMULA = '=[@Sales Amount]*[@[% Commission]]';

    // Note that column headings may contain a non-breaking space, while the formula may not;
    // these still need to match.
    // As compared to StructuredReferenceTest, the last column
    //   "Commission/Amount" contains a slash. See PR #3513.
    protected array $tableData = [
        ["Sales\u{a0}Person", 'Region', "Sales\u{a0}Amount", "%\u{a0}Commission", 'Commission/Amount'],
        ['Joe', 'North', 260, '10%', self::COLUMN_FORMULA],
        ['Robert', 'South', 660, '15%', self::COLUMN_FORMULA],
        ['Michelle', 'East', 940, '15%', self::COLUMN_FORMULA],
        ['Erich', 'West', 410, '12%', self::COLUMN_FORMULA],
        ['Dafna', 'North', 800, '15%', self::COLUMN_FORMULA],
        ['Rob', 'South', 900, '15%', self::COLUMN_FORMULA],
        ['Total'],
    ];

    protected function getSpreadsheet(): Spreadsheet
    {
        $spreadsheet = $this->spreadSheet = new Spreadsheet();
        $workSheet = $spreadsheet->getActiveSheet();
        $workSheet->fromArray($this->tableData, null, 'A1');

        $table = new Table('A1:E8', 'DeptSales');
        $table->setShowTotalsRow(true);
        $table->getColumn('A')->setTotalsRowLabel('Total');
        $workSheet->addTable($table);

        return $spreadsheet;
    }

    protected function tearDown(): void
    {
        if ($this->spreadSheet !== null) {
            $this->spreadSheet->disconnectWorksheets();
            $this->spreadSheet = null;
        }

        parent::tearDown();
    }

    /**
     * @dataProvider structuredReferenceProviderColumnData
     */
    public function testStructuredReferenceColumns(string $expectedCellRange, string $structuredReference): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $structuredReferenceObject = new StructuredReference($structuredReference);
        $cellRange = $structuredReferenceObject->parse($spreadsheet->getActiveSheet()->getCell('E5'));
        self::assertSame($expectedCellRange, $cellRange);
    }

    /**
     * @dataProvider structuredReferenceProviderRowData
     */
    public function testStructuredReferenceRows(string $expectedCellRange, string $structuredReference): void
    {
        $spreadsheet = $this->getSpreadsheet();
        $structuredReferenceObject = new StructuredReference($structuredReference);
        $cellRange = $structuredReferenceObject->parse($spreadsheet->getActiveSheet()->getCell('E5'));
        self::assertSame($expectedCellRange, $cellRange);
    }

    public static function structuredReferenceProviderColumnData(): array
    {
        return [
            // Full table, with no column specified,  means data only, not headers or totals
            'Full table Unqualified' => ['A2:E7', '[]'],
            'Full table Qualified' => ['A2:E7', 'DeptSales[]'],
            // No item identifier, but with a column identifier, means data and header for the column, but no totals
            'Column with no Item Identifier #1' => ['A2:A7', 'DeptSales[[Sales Person]]'],
            'Column with no Item Identifier #2' => ['B2:B7', 'DeptSales[Region]'],
            // Item identifier with no column specified
            'Item Identifier only #1' => ['A1:E1', 'DeptSales[#Headers]'],
            'Item Identifier only #2' => ['A1:E1', 'DeptSales[[#Headers]]'],
            'Item Identifier only #3' => ['A8:E8', 'DeptSales[#Totals]'],
            'Item Identifier only #4' => ['A2:E7', 'DeptSales[#Data]'],
            // Item identifiers and column identifiers
            'Full column' => ['C1:C8', 'DeptSales[[#All],[Sales Amount]]'],
            'Column Header' => ['D1', 'DeptSales[[#Headers],[% Commission]]'],
            'Column Total' => ['B8', 'DeptSales[[#Totals],[Region]]'],
            'Column Range All' => ['C1:D8', 'DeptSales[[#All],[Sales Amount]:[% Commission]]'],
            'Column Range Data' => ['D2:E7', 'DeptSales[[#Data],[% Commission]:[Commission/Amount]]'],
            'Column Range Headers' => ['B1:E1', 'DeptSales[[#Headers],[Region]:[Commission/Amount]]'],
            'Column Range Totals' => ['C8:E8', 'DeptSales[[#Totals],[Sales Amount]:[Commission/Amount]]'],
            'Column Range Headers and Data' => ['D1:D7', 'DeptSales[[#Headers],[#Data],[% Commission]]'],
            'Column Range No Item Identifier' => ['A2:B7', 'DeptSales[[Sales Person]:[Region]]'],
            //            ['C2:C7,E2:E7', 'DeptSales[Sales Amount],DeptSales[Commission Amount]'],
            //            ['B2:C7', 'DeptSales[[Sales Person]:[Sales Amount]] DeptSales[[Region]:[% Commission]]'],
        ];
    }

    public static function structuredReferenceProviderRowData(): array
    {
        return [
            ['E5', 'DeptSales[[#This Row], [Commission/Amount]]'],
            ['E5', 'DeptSales[@Commission/Amount]'],
            ['E5', 'DeptSales[@[Commission/Amount]]'],
            ['C5:D5', 'DeptSales[@[Sales Amount]:[% Commission]]'],
        ];
    }
}
