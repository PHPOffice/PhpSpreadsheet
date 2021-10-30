<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class MirrTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMIRR
     *
     * @param mixed $expectedResult
     */
    public function testMIRR($expectedResult, ...$args): void
    {
        $result = Financial::MIRR(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerMIRR(): array
    {
        return require 'tests/data/Calculation/Financial/MIRR.php';
    }
}
