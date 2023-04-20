<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Engine;

use PhpOffice\PhpSpreadsheet\Calculation\Engine\Operands\StructuredReference;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PHPUnit\Framework\TestCase;

class StructuredReferenceTest extends TestCase
{
    protected Spreadsheet $spreadSheet;

    protected const COLUMN_FORMULA = '=[@Sales Amount]*[@[% Commission]]';

    // Note that column headings may contain a non-breaking space, while the formula may not;
    // these still need to match.
    protected array $tableData = [
        ["Sales\u{a0}Person", 'Region', "Sales\u{a0}Amount", "%\u{a0}Commission", "Commission\u{a0}Amount"],
        ['Joe', 'North', 260, '10%', self::COLUMN_FORMULA],
        ['Robert', 'South', 660, '15%', self::COLUMN_FORMULA],
        ['Michelle', 'East', 940, '15%', self::COLUMN_FORMULA],
        ['Erich', 'West', 410, '12%', self::COLUMN_FORMULA],
        ['Dafna', 'North', 800, '15%', self::COLUMN_FORMULA],
        ['Rob', 'South', 900, '15%', self::COLUMN_FORMULA],
        ['Total'],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->spreadSheet = new Spreadsheet();
        $workSheet = $this->spreadSheet->getActiveSheet();
        $workSheet->fromArray($this->tableData, null, 'A1');

        $table = new Table('A1:E8', 'DeptSales');
        $table->setShowTotalsRow(true);
        $table->getColumn('A')->setTotalsRowLabel('Total');
        $workSheet->addTable($table);
    }

    protected function tearDown(): void
    {
        $this->spreadSheet->disconnectWorksheets();

        parent::tearDown();
    }

    public function testStructuredReferenceInvalidTable(): void
    {
        $cell = $this->spreadSheet->getActiveSheet()->getCell('H5');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Table SalesResults for Structured Reference cannot be located');
        $structuredReferenceObject = new StructuredReference('SalesResults[@[% Commission]]');
        $structuredReferenceObject->parse($cell);
    }

    public function testStructuredReferenceInvalidCellForTable(): void
    {
        $cell = $this->spreadSheet->getActiveSheet()->getCell('H99');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Table for Structured Reference cannot be identified');
        $structuredReferenceObject = new StructuredReference('[@[% Commission]]');
        $structuredReferenceObject->parse($cell);
    }

    /**
     * @dataProvider structuredReferenceProviderColumnData
     */
    public function testStructuredReferenceColumns(string $expectedCellRange, string $structuredReference): void
    {
        $cell = $this->spreadSheet->getActiveSheet()->getCell('E5');

        $structuredReferenceObject = new StructuredReference($structuredReference);
        $cellRange = $structuredReferenceObject->parse($cell);
        self::assertSame($expectedCellRange, $cellRange);
    }

    /**
     * @dataProvider structuredReferenceProviderRowData
     */
    public function testStructuredReferenceRows(string $expectedCellRange, string $structuredReference): void
    {
        $cell = $this->spreadSheet->getActiveSheet()->getCell('E5');

        $structuredReferenceObject = new StructuredReference($structuredReference);
        $cellRange = $structuredReferenceObject->parse($cell);
        self::assertSame($expectedCellRange, $cellRange);
    }

    public function testInvalidStructuredReferenceRow(): void
    {
        $cell = $this->spreadSheet->getActiveSheet()->getCell('E5');

        $this->expectException(Exception::class);
        $this->expectExceptionCode(1);
        $this->expectExceptionMessage('Invalid Structured Reference');
        $this->expectExceptionCode(Exception::CALCULATION_ENGINE_PUSH_TO_STACK);
        $structuredReferenceObject = new StructuredReference('DeptSales[@[Sales]:[%age Commission]]');
        $structuredReferenceObject->parse($cell);
    }

    public function testStructuredReferenceHeadersHidden(): void
    {
        $cell = $this->spreadSheet->getActiveSheet()->getCell('K1');
        $table = $this->spreadSheet->getActiveSheet()->getTableByName('DeptSales');
        /** @var Table $table */
        $structuredReferenceObject = new StructuredReference('DeptSales[[#Headers],[% Commission]]');
        $cellRange = $structuredReferenceObject->parse($cell);
        self::assertSame('D1', $cellRange);

        $table->setShowHeaderRow(false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Table Headers are Hidden, and should not be Referenced');
        $this->expectExceptionCode(Exception::CALCULATION_ENGINE_PUSH_TO_STACK);
        $structuredReferenceObject = new StructuredReference('DeptSales[[#Headers],[% Commission]]');
        $structuredReferenceObject->parse($cell);
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
            'Column Range Data' => ['D2:E7', 'DeptSales[[#Data],[% Commission]:[Commission Amount]]'],
            'Column Range Headers' => ['B1:E1', 'DeptSales[[#Headers],[Region]:[Commission Amount]]'],
            'Column Range Totals' => ['C8:E8', 'DeptSales[[#Totals],[Sales Amount]:[Commission Amount]]'],
            'Column Range Headers and Data' => ['D1:D7', 'DeptSales[[#Headers],[#Data],[% Commission]]'],
            'Column Range No Item Identifier' => ['A2:B7', 'DeptSales[[Sales Person]:[Region]]'],
            //            ['C2:C7,E2:E7', 'DeptSales[Sales Amount],DeptSales[Commission Amount]'],
            //            ['B2:C7', 'DeptSales[[Sales Person]:[Sales Amount]] DeptSales[[Region]:[% Commission]]'],
        ];
    }

    public static function structuredReferenceProviderRowData(): array
    {
        return [
            ['E5', 'DeptSales[[#This Row], [Commission Amount]]'],
            ['E5', 'DeptSales[@Commission Amount]'],
            ['E5', 'DeptSales[@[Commission Amount]]'],
            ['C5:D5', 'DeptSales[@[Sales Amount]:[% Commission]]'],
        ];
    }
}
