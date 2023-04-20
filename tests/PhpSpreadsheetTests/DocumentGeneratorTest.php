<?php

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Calculation\Category as Cat;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical;
use PhpOffice\PhpSpreadsheetInfra\DocumentGenerator;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class DocumentGeneratorTest extends TestCase
{
    /**
     * @dataProvider providerGenerateFunctionListByName
     */
    public function testGenerateFunctionListByName(array $phpSpreadsheetFunctions, string $expected): void
    {
        self::assertEquals($expected, DocumentGenerator::generateFunctionListByName($phpSpreadsheetFunctions));
    }

    /**
     * @dataProvider providerGenerateFunctionListByCategory
     */
    public function testGenerateFunctionListByCategory(array $phpSpreadsheetFunctions, string $expected): void
    {
        self::assertEquals($expected, DocumentGenerator::generateFunctionListByCategory($phpSpreadsheetFunctions));
    }

    public static function providerGenerateFunctionListByName(): array
    {
        return [
            [
                [
                    'ABS' => ['category' => Cat::CATEGORY_MATH_AND_TRIG, 'functionCall' => 'abs'],
                    'AND' => ['category' => Cat::CATEGORY_LOGICAL, 'functionCall' => [Logical::class, 'logicalAnd']],
                    'IFS' => ['category' => Cat::CATEGORY_LOGICAL, 'functionCall' => [Functions::class, 'DUMMY']],
                ],
                <<<'EXPECTED'
                    # Function list by name

                    ## A

                    Excel Function           | Category                       | PhpSpreadsheet Function
                    -------------------------|--------------------------------|--------------------------------------
                    ABS                      | CATEGORY_MATH_AND_TRIG         | abs
                    AND                      | CATEGORY_LOGICAL               | \PhpOffice\PhpSpreadsheet\Calculation\Logical::logicalAnd

                    ## I

                    Excel Function           | Category                       | PhpSpreadsheet Function
                    -------------------------|--------------------------------|--------------------------------------
                    IFS                      | CATEGORY_LOGICAL               | **Not yet Implemented**

                    EXPECTED

            ],
        ];
    }

    public static function providerGenerateFunctionListByCategory(): array
    {
        return [
            [
                [
                    'ABS' => ['category' => Cat::CATEGORY_MATH_AND_TRIG, 'functionCall' => 'abs'],
                    'AND' => ['category' => Cat::CATEGORY_LOGICAL, 'functionCall' => [Logical::class, 'logicalAnd']],
                    'IFS' => ['category' => Cat::CATEGORY_LOGICAL, 'functionCall' => [Functions::class, 'DUMMY']],
                ],
                <<<'EXPECTED'
                    # Function list by category

                    ## CATEGORY_CUBE

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_DATABASE

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_DATE_AND_TIME

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_ENGINEERING

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_FINANCIAL

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_INFORMATION

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_LOGICAL

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------
                    AND                      | \PhpOffice\PhpSpreadsheet\Calculation\Logical::logicalAnd
                    IFS                      | **Not yet Implemented**

                    ## CATEGORY_LOOKUP_AND_REFERENCE

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_MATH_AND_TRIG

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------
                    ABS                      | abs

                    ## CATEGORY_STATISTICAL

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_TEXT_AND_DATA

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_WEB

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    ## CATEGORY_UNCATEGORISED

                    Excel Function           | PhpSpreadsheet Function
                    -------------------------|--------------------------------------

                    EXPECTED

            ],
        ];
    }

    public function testGenerateFunctionBadArray(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $phpSpreadsheetFunctions = [
            'ABS' => ['category' => Cat::CATEGORY_MATH_AND_TRIG, 'functionCall' => 1],
        ];
        DocumentGenerator::generateFunctionListByName($phpSpreadsheetFunctions);
    }
}
