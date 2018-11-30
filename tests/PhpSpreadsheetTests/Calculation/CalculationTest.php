<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PHPUnit\Framework\TestCase;

class CalculationTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    public function tearDown()
    {
        $calculation = Calculation::getInstance();
        $calculation->setLocale('en_us');
    }

    /**
     * @dataProvider providerBinaryComparisonOperation
     *
     * @param mixed $formula
     * @param mixed $expectedResultExcel
     * @param mixed $expectedResultOpenOffice
     */
    public function testBinaryComparisonOperation($formula, $expectedResultExcel, $expectedResultOpenOffice)
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $resultExcel = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResultExcel, $resultExcel, 'should be Excel compatible');

        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);
        $resultOpenOffice = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResultOpenOffice, $resultOpenOffice, 'should be OpenOffice compatible');
    }

    public function providerBinaryComparisonOperation()
    {
        return require 'data/CalculationBinaryComparisonOperation.php';
    }

    /**
     * @dataProvider providerGetFunctions
     *
     * @param string $category
     * @param array|string $functionCall
     * @param string $argumentCount
     */
    public function testGetFunctions($category, $functionCall, $argumentCount)
    {
        self::assertInternalType('callable', $functionCall);
    }

    public function providerGetFunctions()
    {
        return Calculation::getInstance()->getFunctions();
    }

    public function testIsImplemented()
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
    public function testCanLoadAllSupportedLocales($locale)
    {
        $calculation = Calculation::getInstance();
        self::assertTrue($calculation->setLocale($locale));
    }

    public function providerCanLoadAllSupportedLocales()
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
            ['no'],
            ['pl'],
            ['pt'],
            ['pt_br'],
            ['ru'],
            ['sv'],
            ['tr'],
        ];
    }

    public function testDoesHandleXlfnFunctions()
    {
        $calculation = Calculation::getInstance();

        $tree = $calculation->parseFormula('=_xlfn.ISFORMULA(A1)');
        self::assertCount(3, $tree);
        $function = $tree[2];
        self::assertEquals('Function', $function['type']);

        $tree = $calculation->parseFormula('=_xlfn.STDEV.S(A1:B2)');
        self::assertCount(5, $tree);
        $function = $tree[4];
        self::assertEquals('Function', $function['type']);
    }

    public function testFormulaWithOptionalArgumentsAndRequiredCellReferenceShouldPassNullForMissingArguments()
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

    public function testBranchPruningFormulaParsing()
    {
        $calculation = Calculation::getInstance();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(
            [
                ['please +', 'please *', 'increment'],
                [1, 1, 1], // sum is 3
                [3, 3, 3], // product is 27
            ]
        );

        $cell = $sheet->getCell('E5');

        // Very simple formula
        $formula = '=IF(A1="please +",B1)';
        $tokens = $calculation->parseFormula($formula);

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
        $this->assertTrue($foundEqualAssociatedToStoreKey);
        $this->assertTrue($foundConditionalOnB1);

        
        $calculation->flushInstance(); // resets the ids

        //
        // Internal opeartio
        $formula = '=IF(A1="please +",SUM(B1:B3))+IF(A2="please *",PRODUCT(C1:C3), C1)';
        $tokens = $calculation->parseFormula($formula);

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
            $productFunctionCorrectlyTagged = $productFunctionCorrectlyTagged ||
                ($isFunction && $isProductFunction && $correctOnlyIf);
        }
        $this->assertFalse($plusGotTagged,
            'chaining IF( should not affect the external operators');
        $this->assertTrue($productFunctionCorrectlyTagged,
            'function nested inside if should be tagged to be processed only if parent branching requires it');

        $calculation->flushInstance(); // resets the ids

        $formula = '=IF(A1="please +",SUM(B1:B3),1+IF(NOT(A2="please *"),C2-C1,PRODUCT(C1:C3)))';
        $tokens = $calculation->parseFormula($formula);

        $plusCorrectlyTagged = false;
        $productFunctionCorrectlyTagged = false;
        $notFunctionCorrectlyTagged = false;
        foreach($tokens as $token) {
            $value = $token['value'];
            $isPlus = $value == '+';
            $isProductFunction = $value == 'PRODUCT(';
            $isNotFunction = $value == 'NOT(';
            $isOnlyIfNotDepth1 = $token['onlyIfNot'] == 'storeKey-1';
            $isStoreKeyDepth1 = $token['storeKey'] == 'storeKey-1';
            $isOnlyIfNotDepth0 = $token['onlyIfNot'] == 'storeKey-0';

            $plusCorrectlyTagged = $plusCorrectlyTagged ||
                ($isPlus && $isOnlyIfNotDepth0);
            $notFunctionCorrectlyTagged = $notFunctionCorrectlyTagged ||
                ($isNotFunction && $isOnlyIfNotDepth0 && $isStoreKeyDepth1);
            $productFunctionCorrectlyTagged = $productFunctionCorrectlyTagged ||
                ($isProductFunction && $isOnlyIfNotDepth1 && !$isStoreKeyDepth1 && !$isOnlyIfNotDepth0);
        }
        $this->assertTrue($plusCorrectlyTagged);
        $this->assertTrue($productFunctionCorrectlyTagged);
        $this->assertTrue($notFunctionCorrectlyTagged);
    }
}
