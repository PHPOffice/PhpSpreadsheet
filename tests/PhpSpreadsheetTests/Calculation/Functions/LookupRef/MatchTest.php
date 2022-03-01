<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class MatchTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMATCH
     *
     * @param mixed $expectedResult
     */
    public function testMATCH($expectedResult, ...$args): void
    {
        $result = LookupRef::MATCH(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMATCH(): array
    {
        return require 'tests/data/Calculation/LookupRef/MATCH.php';
    }

    /**
     * @dataProvider providerMatchArray
     */
    public function testMatchArray(array $expectedResult, string $values, string $selections): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=MATCH({$values}, {$selections}, 0)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMatchArray(): array
    {
        return [
            'row vector' => [
                [[2, 5, 3]],
                '{"Orange", "Blue", "Yellow"}',
                '{"Red", "Orange", "Yellow", "Green", "Blue"}',
            ],
        ];
    }
}
