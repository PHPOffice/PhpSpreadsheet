<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\ConditionalFormatting;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Exception as ssException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\ConditionalFormatting\CellMatcher;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class CellMatcherTest extends TestCase
{
    protected ?Spreadsheet $spreadsheet = null;

    protected function loadSpreadsheet(): Spreadsheet
    {
        $filename = 'tests/data/Style/ConditionalFormatting/CellMatcher.xlsx';
        $reader = IOFactory::createReader('Xlsx');

        return $reader->load($filename);
    }

    protected function tearDown(): void
    {
        if ($this->spreadsheet !== null) {
            $this->spreadsheet->disconnectWorksheets();
            $this->spreadsheet = null;
        }
    }

    private function confirmString(Worksheet $worksheet, Cell $cell, string $cellAddress): string
    {
        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate()) ?? '';
        if ($cfRange === '') {
            self::fail("{$cellAddress} is not in a Conditional Format range");
        }

        return $cfRange;
    }

    /**
     * @dataProvider basicCellIsComparisonDataProvider
     */
    public function testBasicCellIsComparison(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public static function basicCellIsComparisonDataProvider(): array
    {
        return [
            // Less than/Equal/Greater than with Literal
            'A2' => ['cellIs Comparison', 'A2', [false, false, true]],
            'C3' => ['cellIs Comparison', 'C3', [false, true, false]],
            'E6' => ['cellIs Comparison', 'E6', [true, false, false]],
            // Less than/Equal/Greater than with Cell Reference
            'A12' => ['cellIs Comparison', 'A12', [false, false, true]],
            'C12' => ['cellIs Comparison', 'C12', [false, true, false]],
            'E12' => ['cellIs Comparison', 'E12', [true, false, false]],
            // Compare Text with Cell containing Formula
            'A20' => ['cellIs Comparison', 'A20', [true]],
            'B20' => ['cellIs Comparison', 'B20', [false]],
            // Compare Text with Formula referencing relative cells
            'A24' => ['cellIs Comparison', 'A24', [true]],
            'B24' => ['cellIs Comparison', 'B24', [false]],
            'A25' => ['cellIs Comparison', 'A25', [false]],
            'B25' => ['cellIs Comparison', 'B25', [true]],
            // Compare Cell Greater/Less with Vertical Cell Reference
            'A30' => ['cellIs Comparison', 'A30', [false, true]],
            'A31' => ['cellIs Comparison', 'A31', [true, false]],
            'A32' => ['cellIs Comparison', 'A32', [false, true]],
            'A33' => ['cellIs Comparison', 'A33', [true, false]],
            'A34' => ['cellIs Comparison', 'A34', [false, false]],
            'A35' => ['cellIs Comparison', 'A35', [false, true]],
            'A36' => ['cellIs Comparison', 'A36', [true, false]],
            'A37' => ['cellIs Comparison', 'A37', [true, false]],
        ];
    }

    public function testNotInRange(): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $sheetname = 'cellIs Comparison';
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell('J20');

        $cfRange = $worksheet->getConditionalRange($cell->getCoordinate());
        self::assertNull($cfRange);
    }

    public function testUnknownSheet(): void
    {
        $this->expectException(ssException::class);
        $this->spreadsheet = $this->loadSpreadsheet();
        $sheetname = 'cellIs Comparisonxxx';
        $this->spreadsheet->getSheetByNameOrThrow($sheetname);
    }

    /**
     * @dataProvider rangeCellIsComparisonDataProvider
     */
    public function testRangeCellIsComparison(string $sheetname, string $cellAddress, bool $expectedMatch): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyle = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        $match = $matcher->evaluateConditional($cfStyle[0]);
        self::assertSame($expectedMatch, $match);
    }

    public static function rangeCellIsComparisonDataProvider(): array
    {
        return [
            // Range between Literals
            'A2' => ['cellIs Range Comparison', 'A2', false],
            'A3' => ['cellIs Range Comparison', 'A3', true],
            'A4' => ['cellIs Range Comparison', 'A4', true],
            'A5' => ['cellIs Range Comparison', 'A5', true],
            'A6' => ['cellIs Range Comparison', 'A6', false],
            // Range between Cell References
            'A11' => ['cellIs Range Comparison', 'A11', false],
            'A12' => ['cellIs Range Comparison', 'A12', false],
            'A13' => ['cellIs Range Comparison', 'A13', true],
            // Range between unordered Cell References
            'A17' => ['cellIs Range Comparison', 'A17', true],
            'A18' => ['cellIs Range Comparison', 'A18', true],
            // Range between with Formula
            'A22' => ['cellIs Range Comparison', 'A22', false],
            'A23' => ['cellIs Range Comparison', 'A23', true],
            'A24' => ['cellIs Range Comparison', 'A24', false],
        ];
    }

    /**
     * @dataProvider cellIsExpressionMultipleDataProvider
     */
    public function testCellIsMultipleExpression(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public static function cellIsExpressionMultipleDataProvider(): array
    {
        return [
            // Odd/Even
            'A2' => ['cellIs Expression', 'A2', [false, true]],
            'A3' => ['cellIs Expression', 'A3', [true, false]],
            'B3' => ['cellIs Expression', 'B3', [false, true]],
            'C3' => ['cellIs Expression', 'C3', [true, false]],
            'E4' => ['cellIs Expression', 'E4', [false, true]],
            'E5' => ['cellIs Expression', 'E5', [true, false]],
            'E6' => ['cellIs Expression', 'E6', [false, true]],
        ];
    }

    /**
     * @dataProvider cellIsExpressionDataProvider
     */
    public function testCellIsExpression(string $sheetname, string $cellAddress, bool $expectedMatch): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyle = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        $match = $matcher->evaluateConditional($cfStyle[0]);
        self::assertSame($expectedMatch, $match);
    }

    public static function cellIsExpressionDataProvider(): array
    {
        return [
            // Sales Grid for Country
            ['cellIs Expression', 'A12', false],
            ['cellIs Expression', 'B12', false],
            ['cellIs Expression', 'C12', false],
            ['cellIs Expression', 'D12', false],
            ['cellIs Expression', 'B13', true],
            ['cellIs Expression', 'C13', true],
            ['cellIs Expression', 'B15', true],
            ['cellIs Expression', 'B16', true],
            ['cellIs Expression', 'C17', false],
            // Sales Grid for Country and Quarter
            ['cellIs Expression', 'A22', false],
            ['cellIs Expression', 'B22', false],
            ['cellIs Expression', 'C22', false],
            ['cellIs Expression', 'D22', false],
            ['cellIs Expression', 'B23', true],
            ['cellIs Expression', 'C23', true],
            ['cellIs Expression', 'B25', false],
            ['cellIs Expression', 'B26', true],
            ['cellIs Expression', 'C27', false],
        ];
    }

    /**
     * @dataProvider textExpressionsDataProvider
     */
    public function testTextExpressions(string $sheetname, string $cellAddress, bool $expectedMatch): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyle = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        $match = $matcher->evaluateConditional($cfStyle[0]);
        self::assertSame($expectedMatch, $match);
    }

    public static function textExpressionsDataProvider(): array
    {
        return [
            // Text Begins With Literal
            ['Text Expressions', 'A2', true],
            ['Text Expressions', 'B2', false],
            ['Text Expressions', 'A3', false],
            ['Text Expressions', 'B3', false],
            ['Text Expressions', 'A4', false],
            ['Text Expressions', 'B4', true],
            // Text Ends With Literal
            ['Text Expressions', 'A8', false],
            ['Text Expressions', 'B8', false],
            ['Text Expressions', 'A9', true],
            ['Text Expressions', 'B9', true],
            ['Text Expressions', 'A10', false],
            ['Text Expressions', 'B10', true],
            // Text Contains Literal
            ['Text Expressions', 'A14', true],
            ['Text Expressions', 'B14', false],
            ['Text Expressions', 'A15', true],
            ['Text Expressions', 'B15', true],
            ['Text Expressions', 'A16', false],
            ['Text Expressions', 'B16', true],
            // Text Doesn't Contain Literal
            ['Text Expressions', 'A20', true],
            ['Text Expressions', 'B20', true],
            ['Text Expressions', 'A21', true],
            ['Text Expressions', 'B21', true],
            ['Text Expressions', 'A22', false],
            ['Text Expressions', 'B22', true],
            // Text Begins With Cell Reference
            ['Text Expressions', 'D2', true],
            ['Text Expressions', 'E2', false],
            ['Text Expressions', 'D3', false],
            ['Text Expressions', 'E3', false],
            ['Text Expressions', 'D4', false],
            ['Text Expressions', 'E4', true],
            // Text Ends With Cell Reference
            ['Text Expressions', 'D8', false],
            ['Text Expressions', 'E8', false],
            ['Text Expressions', 'D9', true],
            ['Text Expressions', 'E9', true],
            ['Text Expressions', 'D10', false],
            ['Text Expressions', 'E10', true],
            // Text Contains Cell Reference
            ['Text Expressions', 'D14', true],
            ['Text Expressions', 'E14', false],
            ['Text Expressions', 'D15', true],
            ['Text Expressions', 'E15', true],
            ['Text Expressions', 'D16', false],
            ['Text Expressions', 'E16', true],
            // Text Doesn't Contain Cell Reference
            ['Text Expressions', 'D20', true],
            ['Text Expressions', 'E20', true],
            ['Text Expressions', 'D21', true],
            ['Text Expressions', 'E21', true],
            ['Text Expressions', 'D22', false],
            ['Text Expressions', 'E22', true],
            // Text Begins With Formula
            ['Text Expressions', 'G2', true],
            ['Text Expressions', 'H2', false],
            ['Text Expressions', 'G3', false],
            ['Text Expressions', 'H3', false],
            ['Text Expressions', 'G4', false],
            ['Text Expressions', 'H4', true],
            // Text Ends With Formula
            ['Text Expressions', 'G8', false],
            ['Text Expressions', 'H8', false],
            ['Text Expressions', 'G9', true],
            ['Text Expressions', 'H9', true],
            ['Text Expressions', 'G10', false],
            ['Text Expressions', 'H10', true],
            // Text Contains Formula
            ['Text Expressions', 'G14', true],
            ['Text Expressions', 'H14', false],
            ['Text Expressions', 'G15', true],
            ['Text Expressions', 'H15', true],
            ['Text Expressions', 'G16', false],
            ['Text Expressions', 'H16', true],
            // Text Doesn't Contain Formula
            ['Text Expressions', 'G20', true],
            ['Text Expressions', 'H20', true],
            ['Text Expressions', 'G21', true],
            ['Text Expressions', 'H21', true],
            ['Text Expressions', 'G22', false],
            ['Text Expressions', 'H22', true],
        ];
    }

    /**
     * @dataProvider blanksDataProvider
     */
    public function testBlankExpressions(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public static function blanksDataProvider(): array
    {
        return [
            // Blank/Not Blank
            'A2' => ['Blank Expressions', 'A2', [false, true]],
            'B2' => ['Blank Expressions', 'B2', [true, false]],
            'A3' => ['Blank Expressions', 'A3', [true, false]],
            'B3' => ['Blank Expressions', 'B3', [false, true]],
        ];
    }

    /**
     * @dataProvider errorDataProvider
     */
    public function testErrorExpressions(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public static function errorDataProvider(): array
    {
        return [
            // Error/Not Error
            'C2' => ['Error Expressions', 'C2', [false, true]],
            'C4' => ['Error Expressions', 'C4', [true, false]],
            'C5' => ['Error Expressions', 'C5', [false, true]],
        ];
    }

    /**
     * @dataProvider dateOccurringDataProvider
     */
    public function testDateOccurringExpressions(string $sheetname, string $cellAddress, bool $expectedMatch): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyle = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        $match = $matcher->evaluateConditional($cfStyle[0]);
        self::assertSame($expectedMatch, $match);
    }

    public static function dateOccurringDataProvider(): array
    {
        return [
            // Today
            ['Date Expressions', 'B9', false],
            ['Date Expressions', 'B10', true],
            ['Date Expressions', 'B11', false],
            // Yesterday
            ['Date Expressions', 'C9', true],
            ['Date Expressions', 'C10', false],
            ['Date Expressions', 'C11', false],
            // Tomorrow
            ['Date Expressions', 'D9', false],
            ['Date Expressions', 'D10', false],
            ['Date Expressions', 'D11', true],
            // Last  Daye
            ['Date Expressions', 'E7', false],
            ['Date Expressions', 'E8', true],
            ['Date Expressions', 'E9', true],
            ['Date Expressions', 'E10', true],
            ['Date Expressions', 'E11', false],
        ];
    }

    /**
     * @dataProvider duplicatesDataProvider
     */
    public function testDuplicatesExpressions(string $sheetname, string $cellAddress, array $expectedMatches): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyles = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        foreach ($cfStyles as $cfIndex => $cfStyle) {
            $match = $matcher->evaluateConditional($cfStyle);
            self::assertSame($expectedMatches[$cfIndex], $match);
        }
    }

    public static function duplicatesDataProvider(): array
    {
        return [
            // Duplicate/Unique
            'A2' => ['Duplicates Expressions', 'A2', [true, false]],
            'B2' => ['Duplicates Expressions', 'B2', [false, true]],
            'A4' => ['Duplicates Expressions', 'A4', [true, false]],
            'A5' => ['Duplicates Expressions', 'A5', [false, true]],
            'B5' => ['Duplicates Expressions', 'B5', [true, false]],
            'A9' => ['Duplicates Expressions', 'A9', [true, false]],
            'B9' => ['Duplicates Expressions', 'B9', [false, true]],
        ];
    }

    /**
     * @dataProvider textCrossWorksheetDataProvider
     */
    public function testCrossWorksheetExpressions(string $sheetname, string $cellAddress, bool $expectedMatch): void
    {
        $this->spreadsheet = $this->loadSpreadsheet();
        $worksheet = $this->spreadsheet->getSheetByNameOrThrow($sheetname);
        $cell = $worksheet->getCell($cellAddress);

        $cfRange = $this->confirmString($worksheet, $cell, $cellAddress);
        $cfStyle = $worksheet->getConditionalStyles($cell->getCoordinate());

        $matcher = new CellMatcher($cell, $cfRange);

        $match = $matcher->evaluateConditional($cfStyle[0]);
        self::assertSame($expectedMatch, $match);
    }

    public static function textCrossWorksheetDataProvider(): array
    {
        return [
            // Relative Cell References in another Worksheet
            'A1' => ['CrossSheet References', 'A1', false],
            'A2' => ['CrossSheet References', 'A2', false],
            'A3' => ['CrossSheet References', 'A3', true],
            'A4' => ['CrossSheet References', 'A4', false],
            'A5' => ['CrossSheet References', 'A5', false],
        ];
    }
}
