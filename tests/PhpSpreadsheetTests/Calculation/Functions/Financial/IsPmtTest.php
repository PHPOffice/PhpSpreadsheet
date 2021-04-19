<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class IsPmtTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerISPMT
     *
     * @param mixed $expectedResult
     */
    public function testISPMT($expectedResult, ...$args): void
    {
        $result = Financial::ISPMT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerISPMT(): array
    {
        return require 'tests/data/Calculation/Financial/ISPMT.php';
    }
}
