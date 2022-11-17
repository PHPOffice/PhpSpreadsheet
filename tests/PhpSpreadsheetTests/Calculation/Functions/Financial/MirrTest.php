<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class MirrTest extends TestCase
{
    /**
     * @dataProvider providerMIRR
     *
     * @param mixed $expectedResult
     */
    public function testMIRR($expectedResult, ...$args): void
    {
        $result = Financial\CashFlow\Variable\Periodic::modifiedRate(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerMIRR(): array
    {
        return require 'tests/data/Calculation/Financial/MIRR.php';
    }
}
