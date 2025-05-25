<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Category as Cat;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations;
use PhpOffice\PhpSpreadsheetInfra\DocumentGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class DocumentGeneratorTest extends TestCase
{
    private static bool $succeededByName = false;

    private static bool $succeededByCategory = false;

    /** @param array<string, array{category: string, functionCall: array<string>|string, argumentCount: string, passCellReference?: bool, passByReference?: array<bool>, custom?: bool}> $phpSpreadsheetFunctions */
    #[DataProvider('providerGenerateFunctionListByName')]
    public function testGenerateFunctionListByName(array $phpSpreadsheetFunctions, string $expected): void
    {
        self::assertEquals($expected, DocumentGenerator::generateFunctionListByName($phpSpreadsheetFunctions));
        self::$succeededByName = true;
    }

    /** @param array<string, array{category: string, functionCall: array<string>|string, argumentCount: string, passCellReference?: bool, passByReference?: array<bool>, custom?: bool}> $phpSpreadsheetFunctions */
    #[DataProvider('providerGenerateFunctionListByCategory')]
    public function testGenerateFunctionListByCategory(array $phpSpreadsheetFunctions, string $expected): void
    {
        self::assertEquals($expected, DocumentGenerator::generateFunctionListByCategory($phpSpreadsheetFunctions));
        self::$succeededByCategory = true;
    }

    public static function providerGenerateFunctionListByName(): array
    {
        return [
            [
                [
                    'ABS' => ['category' => Cat::CATEGORY_MATH_AND_TRIG, 'functionCall' => 'abs'],
                    'AND' => ['category' => Cat::CATEGORY_LOGICAL, 'functionCall' => [Operations::class, 'logicalAnd']],
                    'IFS' => ['category' => Cat::CATEGORY_LOGICAL, 'functionCall' => [Functions::class, 'DUMMY']],
                ],
                <<<'EXPECTED'
                    # Function list by name

                    A more compact list can be found [here](./function-list-by-name-compact.md)


                    ## A

                    Excel Function           | Category                       | PhpSpreadsheet Function
                    -------------------------|--------------------------------|--------------------------------------
                    ABS                      | CATEGORY_MATH_AND_TRIG         | abs
                    AND                      | CATEGORY_LOGICAL               | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::logicalAnd

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
                    'AND' => ['category' => Cat::CATEGORY_LOGICAL, 'functionCall' => [Operations::class, 'logicalAnd']],
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
                    AND                      | \PhpOffice\PhpSpreadsheet\Calculation\Logical\Operations::logicalAnd
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

                    ## CATEGORY_MICROSOFT_INTERNAL

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
        // Phpstan is right to complain about next line,
        // but we still need to make sure it is handled correctly at run time.
        DocumentGenerator::generateFunctionListByName(
            $phpSpreadsheetFunctions //* @phpstan-ignore-line
        );
    }

    public function testGenerateDocuments(): void
    {
        if (!self::$succeededByName || !self::$succeededByCategory) {
            self::markTestSkipped('Not run because prior test failed');
        }
        $directory = 'docs/references/';
        $phpSpreadsheetFunctions = Calculation::getFunctions();
        ksort($phpSpreadsheetFunctions);

        self::assertNotFalse(file_put_contents(
            $directory . 'function-list-by-category.md',
            DocumentGenerator::generateFunctionListByCategory(
                $phpSpreadsheetFunctions
            )
        ));
        self::assertNotFalse(file_put_contents(
            $directory . 'function-list-by-name.md',
            DocumentGenerator::generateFunctionListByName(
                $phpSpreadsheetFunctions
            )
        ));
        self::assertNotFalse(file_put_contents(
            $directory . 'function-list-by-name-compact.md',
            DocumentGenerator::generateFunctionListByName(
                $phpSpreadsheetFunctions,
                true
            )
        ));
    }
}
