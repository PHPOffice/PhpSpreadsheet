<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class CumIpmtTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCUMIPMT
     *
     * @param mixed $expectedResult
     */
    public function testCUMIPMT($expectedResult, ...$args): void
    {
        $result = Financial::CUMIPMT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerCUMIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/CUMIPMT.php';
    }
}
