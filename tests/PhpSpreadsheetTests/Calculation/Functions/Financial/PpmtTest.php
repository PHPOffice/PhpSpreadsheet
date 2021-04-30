<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class PpmtTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPPMT
     *
     * @param mixed $expectedResult
     */
    public function testPPMT($expectedResult, array $args): void
    {
        $result = Financial::PPMT(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPPMT(): array
    {
        return require 'tests/data/Calculation/Financial/PPMT.php';
    }
}
