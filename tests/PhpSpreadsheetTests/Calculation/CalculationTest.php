<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CalculationTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    /**
     * @var string
     */
    private $locale;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $calculation = Calculation::getInstance();
        $this->locale = $calculation->getLocale();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        $calculation = Calculation::getInstance();
        $calculation->setLocale($this->locale);
    }

    /**
     * @dataProvider providerBinaryComparisonOperation
     *
     * @param mixed $formula
     * @param mixed $expectedResultExcel
     * @param mixed $expectedResultOpenOffice
     */
    public function testBinaryComparisonOperation($formula, $expectedResultExcel, $expectedResultOpenOffice): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $resultExcel = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResultExcel, $resultExcel, 'should be Excel compatible');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $resultOpenOffice = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResultOpenOffice, $resultOpenOffice, 'should be OpenOffice compatible');
    }

    public function providerBinaryComparisonOperation(): array
    {
        return require 'tests/data/CalculationBinaryComparisonOperation.php';
    }

    /**
     * @dataProvider providerGetFunctions
     *
     * @param string $category
     * @param array|string $functionCall
     * @param string $argumentCount
     */
    public function testGetFunctions($category, $functionCall, $argumentCount): void
    {
        self::assertIsCallable($functionCall);
    }

    public function providerGetFunctions(): array
    {
        return Calculation::getInstance()->getFunctions();
    }

    public function testIsImplemented(): void
    {
        $calculation = Calculation::getInstance();
        self::assertFalse($calculation->isImplemented('non-existing-function'));
        self::assertFalse($calculation->isImplemented('AREAS'));
        self::assertTrue($calculation->isImplemented('coUNt'));
        self::assertTrue($calculation->isImplemented('abs'));
    }

    /**
     * @dataProvider providerCanLoadAllSupportedLocales
     *
     * @param string $locale
     */
    public function testCanLoadAllSupportedLocales($locale): void
    {
        $calculation = Calculation::getInstance();
        self::assertTrue($calculation->setLocale($locale));
    }

    public function testInvalidLocaleReturnsFalse(): void
    {
        $calculation = Calculation::getInstance();
        self::assertFalse($calculation->setLocale('xx'));
    }

    public function providerCanLoadAllSupportedLocales(): array
    {
        return [
            ['bg'],
            ['cs'],
            ['da'],
            ['de'],
            ['en_us'],
            ['es'],
            ['fi'],
            ['fr'],
            ['hu'],
            ['it'],
            ['nl'],
            ['nb'],
            ['pl'],
            ['pt'],
            ['pt_br'],
            ['ru'],
            ['sv'],
            ['tr'],
        ];
    }

    public function testDoesHandleXlfnFunctions(): void
    {
        $calculation = Calculation::getInstance();

        $tree = $calculation->parseFormula('=_xlfn.ISFORMULA(A1)');
        self::assertIsArray($tree);
        self::assertCount(3, $tree);
        $function = $tree[2];
        self::assertEquals('Function', $function['type']);

        $tree = $calculation->parseFormula('=_xlfn.STDEV.S(A1:B2)');
        self::assertIsArray($tree);
        self::assertCount(5, $tree);
        $function = $tree[4];
        self::assertEquals('Function', $function['type']);
    }

    public function testFormulaWithOptionalArgumentsAndRequiredCellReferenceShouldPassNullForMissingArguments(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(
            [
                [1, 2, 3],
                [4, 5, 6],
                [7, 8, 9],
            ]
        );

        $cell = $sheet->getCell('E5');
        $cell->setValue('=OFFSET(D3, -1, -2, 1, 1)');
        self::assertEquals(5, $cell->getCalculatedValue(), 'with all arguments');

        $cell = $sheet->getCell('F6');
        $cell->setValue('=OFFSET(D3, -1, -2)');
        self::assertEquals(5, $cell->getCalculatedValue(), 'missing arguments should be filled with null');
    }

    public function testCellSetAsQuotedText(): void
    {
        $spreadsheet = new Spreadsheet();
        $workSheet = $spreadsheet->getActiveSheet();
        $cell = $workSheet->getCell('A1');

        $cell->setValue("=cmd|'/C calc'!A0");
        $cell->getStyle()->setQuotePrefix(true);

        self::assertEquals("=cmd|'/C calc'!A0", $cell->getCalculatedValue());

        $cell2 = $workSheet->getCell('A2');
        $cell2->setValueExplicit('ABC', DataType::TYPE_FORMULA);
        self::assertEquals('ABC', $cell2->getCalculatedValue());

        $cell3 = $workSheet->getCell('A3');
        $cell3->setValueExplicit('=', DataType::TYPE_FORMULA);
        self::assertEquals('', $cell3->getCalculatedValue());
    }

    public function testCellWithDdeExpresion(): void
    {
        $spreadsheet = new Spreadsheet();
        $workSheet = $spreadsheet->getActiveSheet();
        $cell = $workSheet->getCell('A1');

        $cell->setValue("=cmd|'/C calc'!A0");

        self::assertEquals("=cmd|'/C calc'!A0", $cell->getCalculatedValue());
    }

    public function testCellWithFormulaTwoIndirect(): void
    {
        $spreadsheet = new Spreadsheet();
        $workSheet = $spreadsheet->getActiveSheet();
        $cell1 = $workSheet->getCell('A1');
        $cell1->setValue('2');
        $cell2 = $workSheet->getCell('B1');
        $cell2->setValue('3');
        $cell2 = $workSheet->getCell('C1');
        $cell2->setValue('4');
        $cell3 = $workSheet->getCell('D1');
        $cell3->setValue('=SUM(INDIRECT("A"&ROW()),INDIRECT("B"&ROW()),INDIRECT("C"&ROW()))');

        self::assertEquals('9', $cell3->getCalculatedValue());
    }

    public function testBranchPruningFormulaParsingSimpleCase(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->flushInstance(); // resets the ids

        // Very simple formula
        $formula = '=IF(A1="please +",B1)';
        $tokens = $calculation->parseFormula($formula);
        self::assertIsArray($tokens);

        $foundEqualAssociatedToStoreKey = false;
        $foundConditionalOnB1 = false;
        foreach ($tokens as $token) {
            $isBinaryOperator = $token['type'] == 'Binary Operator';
            $isEqual = $token['value'] == '=';
            $correctStoreKey = ($token['storeKey'] ?? '') == 'storeKey-0';
            $correctOnlyIf = ($token['onlyIf'] ?? '') == 'storeKey-0';
            $isB1Reference = ($token['reference'] ?? '') == 'B1';

            $foundEqualAssociatedToStoreKey = $foundEqualAssociatedToStoreKey ||
                ($isBinaryOperator && $isEqual && $correctStoreKey);

            $foundConditionalOnB1 = $foundConditionalOnB1 ||
                ($isB1Reference && $correctOnlyIf);
        }
        self::assertTrue($foundEqualAssociatedToStoreKey);
        self::assertTrue($foundConditionalOnB1);
    }

    public function testBranchPruningFormulaParsingMultipleIfsCase(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->flushInstance(); // resets the ids

        //
        // Internal operation
        $formula = '=IF(A1="please +",SUM(B1:B3))+IF(A2="please *",PRODUCT(C1:C3), C1)';
        $tokens = $calculation->parseFormula($formula);
        self::assertIsArray($tokens);

        $plusGotTagged = false;
        $productFunctionCorrectlyTagged = false;
        foreach ($tokens as $token) {
            $isBinaryOperator = $token['type'] == 'Binary Operator';
            $isPlus = $token['value'] == '+';
            $anyStoreKey = isset($token['storeKey']);
            $anyOnlyIf = isset($token['onlyIf']);
            $anyOnlyIfNot = isset($token['onlyIfNot']);
            $plusGotTagged = $plusGotTagged ||
                ($isBinaryOperator && $isPlus &&
                    ($anyStoreKey || $anyOnlyIfNot || $anyOnlyIf));

            $isFunction = $token['type'] == 'Function';
            $isProductFunction = $token['value'] == 'PRODUCT(';
            $correctOnlyIf = ($token['onlyIf'] ?? '') == 'storeKey-1';
            $productFunctionCorrectlyTagged = $productFunctionCorrectlyTagged || ($isFunction && $isProductFunction && $correctOnlyIf);
        }
        self::assertFalse($plusGotTagged, 'chaining IF( should not affect the external operators');
        self::assertTrue($productFunctionCorrectlyTagged, 'function nested inside if should be tagged to be processed only if parent branching requires it');
    }

    public function testBranchPruningFormulaParingNestedIfCase(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->flushInstance(); // resets the ids

        $formula = '=IF(A1="please +",SUM(B1:B3),1+IF(NOT(A2="please *"),C2-C1,PRODUCT(C1:C3)))';
        $tokens = $calculation->parseFormula($formula);
        self::assertIsArray($tokens);

        $plusCorrectlyTagged = false;
        $productFunctionCorrectlyTagged = false;
        $notFunctionCorrectlyTagged = false;
        $findOneOperandCountTagged = false;
        foreach ($tokens as $token) {
            $value = $token['value'];
            $isPlus = $value == '+';
            $isProductFunction = $value == 'PRODUCT(';
            $isNotFunction = $value == 'NOT(';
            $isIfOperand = $token['type'] == 'Operand Count for Function IF()';
            $isOnlyIfNotDepth1 = (array_key_exists('onlyIfNot', $token) ? $token['onlyIfNot'] : null) == 'storeKey-1';
            $isStoreKeyDepth1 = (array_key_exists('storeKey', $token) ? $token['storeKey'] : null) == 'storeKey-1';
            $isOnlyIfNotDepth0 = (array_key_exists('onlyIfNot', $token) ? $token['onlyIfNot'] : null) == 'storeKey-0';

            $plusCorrectlyTagged = $plusCorrectlyTagged || ($isPlus && $isOnlyIfNotDepth0);
            $notFunctionCorrectlyTagged = $notFunctionCorrectlyTagged || ($isNotFunction && $isOnlyIfNotDepth0 && $isStoreKeyDepth1);
            $productFunctionCorrectlyTagged = $productFunctionCorrectlyTagged || ($isProductFunction && $isOnlyIfNotDepth1 && !$isStoreKeyDepth1 && !$isOnlyIfNotDepth0);
            $findOneOperandCountTagged = $findOneOperandCountTagged || ($isIfOperand && $isOnlyIfNotDepth0);
        }
        self::assertTrue($plusCorrectlyTagged);
        self::assertTrue($productFunctionCorrectlyTagged);
        self::assertTrue($notFunctionCorrectlyTagged);
    }

    public function testBranchPruningFormulaParsingNoArgumentFunctionCase(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->flushInstance(); // resets the ids

        $formula = '=IF(AND(TRUE(),A1="please +"),2,3)';
        // this used to raise a parser error, we keep it even though we don't
        // test the output
        $calculation->parseFormula($formula);
        self::assertTrue(true);
    }

    public function testBranchPruningFormulaParsingInequalitiesConditionsCase(): void
    {
        $calculation = Calculation::getInstance();
        $calculation->flushInstance(); // resets the ids

        $formula = '=IF(A1="flag",IF(A2<10, 0) + IF(A3<10000, 0))';
        $tokens = $calculation->parseFormula($formula);
        self::assertIsArray($tokens);

        $properlyTaggedPlus = false;
        foreach ($tokens as $token) {
            $isPlus = $token['value'] === '+';
            $hasOnlyIf = !empty($token['onlyIf']);

            $properlyTaggedPlus = $properlyTaggedPlus ||
                ($isPlus && $hasOnlyIf);
        }
        self::assertTrue($properlyTaggedPlus);
    }

    /**
     * @param mixed $expectedResult
     * @param mixed $dataArray
     * @param string $formula
     * @param string $cellCoordinates where to put the formula
     * @param string[] $shouldBeSetInCacheCells coordinates of cells that must
     *  be set in cache
     * @param string[] $shouldNotBeSetInCacheCells coordinates of cells that must
     *  not be set in cache because of pruning
     *
     * @dataProvider dataProviderBranchPruningFullExecution
     */
    public function testFullExecution(
        $expectedResult,
        $dataArray,
        $formula,
        $cellCoordinates,
        $shouldBeSetInCacheCells = [],
        $shouldNotBeSetInCacheCells = []
    ): void {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($dataArray);
        $cell = $sheet->getCell($cellCoordinates);
        $calculation = Calculation::getInstance($cell->getWorksheet()->getParent());

        $cell->setValue($formula);
        $calculated = $cell->getCalculatedValue();
        self::assertEquals($expectedResult, $calculated);

        // this mostly to ensure that at least some cells are cached
        foreach ($shouldBeSetInCacheCells as $setCell) {
            unset($inCache);
            $calculation->getValueFromCache('Worksheet!' . $setCell, $inCache);
            self::assertNotEmpty($inCache);
        }

        foreach ($shouldNotBeSetInCacheCells as $notSetCell) {
            unset($inCache);
            $calculation->getValueFromCache('Worksheet!' . $notSetCell, $inCache);
            self::assertEmpty($inCache);
        }

        $calculation->disableBranchPruning();
        $calculated = $cell->getCalculatedValue();
        self::assertEquals($expectedResult, $calculated);
    }

    public function dataProviderBranchPruningFullExecution(): array
    {
        return require 'tests/data/Calculation/Calculation.php';
    }

    public function testUnknownFunction(): void
    {
        $workbook = new Spreadsheet();
        $sheet = $workbook->getActiveSheet();
        $sheet->setCellValue('A1', '=gzorg()');
        $sheet->setCellValue('A2', '=mode.gzorg(1)');
        $sheet->setCellValue('A3', '=gzorg(1,2)');
        $sheet->setCellValue('A4', '=3+IF(gzorg(),1,2)');
        self::assertEquals('#NAME?', $sheet->getCell('A1')->getCalculatedValue());
        self::assertEquals('#NAME?', $sheet->getCell('A2')->getCalculatedValue());
        self::assertEquals('#NAME?', $sheet->getCell('A3')->getCalculatedValue());
        self::assertEquals('#NAME?', $sheet->getCell('A4')->getCalculatedValue());
    }
}
