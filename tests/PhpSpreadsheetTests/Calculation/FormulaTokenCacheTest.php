<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class FormulaTokenCacheTest extends TestCase
{
    private Spreadsheet $spreadsheet;

    private Calculation $calculation;

    protected function setUp(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $this->calculation = Calculation::getInstance($this->spreadsheet);
        $this->calculation->setFormulaTokenCacheMaxSize(1000);
    }

    protected function tearDown(): void
    {
        $this->calculation->clearFormulaTokenCache();
        $this->spreadsheet->disconnectWorksheets();
    }

    public function testCachedResultMatchesUncachedResult(): void
    {
        $formula = '=1+2';

        // First call: uncached (cold cache)
        $firstResult = $this->calculation->parseFormula($formula);

        // Second call: should come from cache
        $secondResult = $this->calculation->parseFormula($formula);

        self::assertSame($firstResult, $secondResult);
    }

    public function testIdenticalFormulasReuseCache(): void
    {
        $formula = '=SUM(A1:B2)';

        self::assertSame(0, $this->calculation->getFormulaTokenCacheSize());

        $this->calculation->parseFormula($formula);
        self::assertSame(1, $this->calculation->getFormulaTokenCacheSize());

        // Parsing the same formula again should not increase cache size
        $this->calculation->parseFormula($formula);
        self::assertSame(1, $this->calculation->getFormulaTokenCacheSize());
    }

    public function testCacheCanBeCleared(): void
    {
        $this->calculation->parseFormula('=1+2');
        $this->calculation->parseFormula('=3*4');
        self::assertSame(2, $this->calculation->getFormulaTokenCacheSize());

        $this->calculation->clearFormulaTokenCache();
        self::assertSame(0, $this->calculation->getFormulaTokenCacheSize());
    }

    public function testDifferentFormulasGetSeparateCacheEntries(): void
    {
        $formula1 = '=1+2';
        $formula2 = '=3*4';
        $formula3 = '=SUM(A1:A10)';

        $result1 = $this->calculation->parseFormula($formula1);
        $result2 = $this->calculation->parseFormula($formula2);
        $result3 = $this->calculation->parseFormula($formula3);

        self::assertSame(3, $this->calculation->getFormulaTokenCacheSize());

        // Results should be different for different formulas
        self::assertNotEquals($result1, $result2);
        self::assertNotEquals($result1, $result3);
        self::assertNotEquals($result2, $result3);

        // Each formula still returns correct cached result
        self::assertSame($result1, $this->calculation->parseFormula($formula1));
        self::assertSame($result2, $this->calculation->parseFormula($formula2));
        self::assertSame($result3, $this->calculation->parseFormula($formula3));

        // Cache size should not have increased
        self::assertSame(3, $this->calculation->getFormulaTokenCacheSize());
    }

    public function testCacheWorksAcrossMultipleCalculationCalls(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 10);
        $sheet->setCellValue('A2', 20);
        $sheet->setCellValue('B1', '=A1+A2');
        $sheet->setCellValue('B2', '=A1+A2');

        $this->calculation->clearFormulaTokenCache();

        // Parse the formula used in both cells
        $formula = '=A1+A2';
        $firstResult = $this->calculation->parseFormula($formula);
        self::assertSame(1, $this->calculation->getFormulaTokenCacheSize());

        // Calculating different cells with the same formula structure
        $valueB1 = $sheet->getCell('B1')->getCalculatedValue();
        $valueB2 = $sheet->getCell('B2')->getCalculatedValue();

        self::assertSame(30, $valueB1);
        self::assertSame(30, $valueB2);

        // Cache should still return the same parsed result for the formula
        $cachedResult = $this->calculation->parseFormula($formula);
        self::assertSame($firstResult, $cachedResult);
    }

    public function testCacheHandlesComplexFormulas(): void
    {
        $formulas = [
            '=IF(A1>0,A1*2,0)',
            '=VLOOKUP(A1,B1:C10,2,FALSE)',
            '="Hello"&" "&"World"',
            '=SUM(A1:A10)/COUNT(A1:A10)',
            '=-A1%',
        ];

        foreach ($formulas as $formula) {
            $result = $this->calculation->parseFormula($formula);
            self::assertIsArray($result, "Formula {$formula} should parse to an array");
            self::assertNotEmpty($result, "Formula {$formula} should produce non-empty tokens");
        }

        self::assertSame(count($formulas), $this->calculation->getFormulaTokenCacheSize());

        // Verify each formula returns the same result on subsequent calls
        foreach ($formulas as $formula) {
            $first = $this->calculation->parseFormula($formula);
            $second = $this->calculation->parseFormula($formula);
            self::assertSame($first, $second, "Cached result should match for {$formula}");
        }

        // Cache size should remain unchanged
        self::assertSame(count($formulas), $this->calculation->getFormulaTokenCacheSize());
    }

    public function testCacheEvictsWhenFull(): void
    {
        $this->calculation->setFormulaTokenCacheMaxSize(100);

        // Fill the cache beyond its max size
        for ($i = 0; $i < 101; ++$i) {
            $this->calculation->parseFormula("={$i}+1");
        }

        // The cache should have been cleared and then started re-filling
        // After clearing at 100, the 101st entry was added, so size should be 1
        self::assertSame(1, $this->calculation->getFormulaTokenCacheSize());
    }

    public function testNonFormulaStringsReturnEmptyArrayAndAreNotCached(): void
    {
        $result = $this->calculation->parseFormula('not a formula');
        self::assertSame([], $result);

        // Non-formulas (no = prefix) return early before caching
        self::assertSame(0, $this->calculation->getFormulaTokenCacheSize());
    }

    public function testCacheDisabledByDefault(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        // Default max size is 0 (disabled)
        self::assertSame(0, $calculation->getFormulaTokenCacheMaxSize());

        $calculation->parseFormula('=1+2');
        self::assertSame(0, $calculation->getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetMaxSizeEnablesCache(): void
    {
        $spreadsheet = new Spreadsheet();
        $calculation = Calculation::getInstance($spreadsheet);

        $calculation->setFormulaTokenCacheMaxSize(500);
        self::assertSame(500, $calculation->getFormulaTokenCacheMaxSize());

        $calculation->parseFormula('=1+2');
        self::assertSame(1, $calculation->getFormulaTokenCacheSize());

        $spreadsheet->disconnectWorksheets();
    }

    public function testSetMaxSizeToZeroClearsCache(): void
    {
        $this->calculation->parseFormula('=1+2');
        self::assertSame(1, $this->calculation->getFormulaTokenCacheSize());

        $this->calculation->setFormulaTokenCacheMaxSize(0);
        self::assertSame(0, $this->calculation->getFormulaTokenCacheSize());
        self::assertSame(0, $this->calculation->getFormulaTokenCacheMaxSize());
    }
}
