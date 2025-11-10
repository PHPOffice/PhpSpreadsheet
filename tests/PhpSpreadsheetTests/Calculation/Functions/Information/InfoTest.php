<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Information;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerINFO')]
    public function testINFO(mixed $expectedResult, string $typeText): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=INFO($typeText)";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerINFO(): array
    {
        return require 'tests/data/Calculation/Information/INFO.php';
    }
}
