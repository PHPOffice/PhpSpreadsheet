<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    /**
     * @dataProvider providerADDRESS
     */
    public function testADDRESS(mixed $expectedResult, mixed ...$args): void
    {
        $result = LookupRef\Address::cell(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerADDRESS(): array
    {
        return require 'tests/data/Calculation/LookupRef/ADDRESS.php';
    }

    /**
     * @dataProvider providerAddressArray
     */
    public function testAddressArray(array $expectedResult, string $argument1, string $argument2): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=ADDRESS({$argument1}, {$argument2}, 4)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerAddressArray(): array
    {
        return [
            'row/column vectors' => [
                [['A1', 'B1', 'C1'], ['A2', 'B2', 'C2'], ['A3', 'B3', 'C3']],
                '{1; 2; 3}',
                '{1, 2, 3}',
            ],
        ];
    }
}
