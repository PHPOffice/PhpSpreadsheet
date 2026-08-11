<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AssociativityTest extends TestCase
{
    #[DataProvider('providerAssociativity')]
    public function testAssociativity(mixed $expectedResult, string $formula): void
    {
        $result = Calculation::getInstance()->calculateFormula($formula);
        if (is_float($expectedResult)) {
            self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
        } else {
            self::assertSame($expectedResult, $result);
        }
    }

    public static function providerAssociativity(): array
    {
        return [
            'Excel exponentiation is left-associative unlike Php and pure math' => [4096, '=4^2^3'],
            'multiplication' => [24, '=4*2*3'],
            'division' => [1, '=8/4/2'],
            'addition' => [9, '=4+2+3'],
            'subtraction' => [-1, '=4-2-3'],
            'concatenation' => ['abc', '="a"&"b"&"c"'],
        ];
    }
}
