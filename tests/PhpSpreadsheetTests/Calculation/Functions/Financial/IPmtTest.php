<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class IPmtTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerIPMT
     *
     * @param mixed $expectedResult
     */
    public function testIPMT($expectedResult, ...$args): void
    {
        $result = Financial::IPMT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerIPMT(): array
    {
        return require 'tests/data/Calculation/Financial/IPMT.php';
    }
}
