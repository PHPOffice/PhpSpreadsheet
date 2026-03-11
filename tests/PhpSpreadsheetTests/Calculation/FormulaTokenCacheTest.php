<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class FormulaTokenCacheTest extends TestCase
{
    protected function setUp(): void
    {
        Calculation::clearFormulaTokenCache();
    }

    protected function tearDown(): void
    {
        Calculation::clearFormulaTokenCache();
    }

    public function testCachedResultMatchesUncachedResult(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        $formula = '=1+2';

        // First call: uncached (cold cache)
        $firstResult = $calculation->parseFormula($formula);

        // Second call: should come from cache
        $secondResult = $calculation->parseFormula($formula);

        self::assertSame($firstResult, $secondResult);
        $spreadsheet->disconnectWorksheets();
    }

    public function testIdenticalFormulasReuseCache(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        $formula = '=SUM(A1:B2)';

        self::assertSame(0, Calculation::getFormulaTokenCacheSize());

        $calculation->parseFormula($formula);
        self::assertSame(1, Calculation::getFormulaTokenCacheSize());

        // Parsing the same formula again should not increase cache size
        $calculation->parseFormula($formula);
        self::assertSame(1, Calculation::getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCacheCanBeCleared(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        $calculation->parseFormula('=1+2');
        $calculation->parseFormula('=3*4');
        self::assertSame(2, Calculation::getFormulaTokenCacheSize());

        Calculation::clearFormulaTokenCache();
        self::assertSame(0, Calculation::getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }

    public function testDifferentFormulasGetSeparateCacheEntries(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        $formula1 = '=1+2';
        $formula2 = '=3*4';
        $formula3 = '=SUM(A1:A10)';

        $result1 = $calculation->parseFormula($formula1);
        $result2 = $calculation->parseFormula($formula2);
        $result3 = $calculation->parseFormula($formula3);

        self::assertSame(3, Calculation::getFormulaTokenCacheSize());

        // Results should be different for different formulas
        self::assertNotEquals($result1, $result2);
        self::assertNotEquals($result1, $result3);
        self::assertNotEquals($result2, $result3);

        // Each formula still returns correct cached result
        self::assertSame($result1, $calculation->parseFormula($formula1));
        self::assertSame($result2, $calculation->parseFormula($formula2));
        self::assertSame($result3, $calculation->parseFormula($formula3));

        // Cache size should not have increased
        self::assertSame(3, Calculation::getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCacheWorksAcrossMultipleCalculationCalls(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 10);
        $sheet->setCellValue('A2', 20);
        $sheet->setCellValue('B1', '=A1+A2');
        $sheet->setCellValue('B2', '=A1+A2');

        $calculation = Calculation::getInstance($spreadsheet);

        Calculation::clearFormulaTokenCache();

        // Parse the formula used in both cells
        $formula = '=A1+A2';
        $firstResult = $calculation->parseFormula($formula);
        self::assertSame(1, Calculation::getFormulaTokenCacheSize());

        // Calculating different cells with the same formula structure
        $valueB1 = $sheet->getCell('B1')->getCalculatedValue();
        $valueB2 = $sheet->getCell('B2')->getCalculatedValue();

        self::assertSame(30, $valueB1);
        self::assertSame(30, $valueB2);

        // Cache should still return the same parsed result for the formula
        $cachedResult = $calculation->parseFormula($formula);
        self::assertSame($firstResult, $cachedResult);

        $spreadsheet->disconnectWorksheets();
    }

    public function testCacheHandlesComplexFormulas(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        $formulas = [
            '=IF(A1>0,A1*2,0)',
            '=VLOOKUP(A1,B1:C10,2,FALSE)',
            '="Hello"&" "&"World"',
            '=SUM(A1:A10)/COUNT(A1:A10)',
            '=-A1%',
        ];

        foreach ($formulas as $formula) {
            $result = $calculation->parseFormula($formula);
            self::assertIsArray($result, "Formula {$formula} should parse to an array");
            self::assertNotEmpty($result, "Formula {$formula} should produce non-empty tokens");
        }

        self::assertSame(count($formulas), Calculation::getFormulaTokenCacheSize());

        // Verify each formula returns the same result on subsequent calls
        foreach ($formulas as $formula) {
            $first = $calculation->parseFormula($formula);
            $second = $calculation->parseFormula($formula);
            self::assertSame($first, $second, "Cached result should match for {$formula}");
        }

        // Cache size should remain unchanged
        self::assertSame(count($formulas), Calculation::getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }

    public function testCacheEvictsWhenFull(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        // Fill the cache beyond its max size by generating unique formulas
        // The cache max is 1000, so generate 1001 unique formulas
        for ($i = 0; $i < 1001; ++$i) {
            $calculation->parseFormula("={$i}+1");
        }

        // The cache should have been cleared and then started re-filling
        // After clearing at 1000, the 1001st entry was added, so size should be 1
        self::assertSame(1, Calculation::getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }

    public function testNonFormulaStringsReturnEmptyArrayAndAreNotCached(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        $result = $calculation->parseFormula('not a formula');
        self::assertSame([], $result);

        // Non-formulas (no = prefix) return early before caching
        self::assertSame(0, Calculation::getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }
}
