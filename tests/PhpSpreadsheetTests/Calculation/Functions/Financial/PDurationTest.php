<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class PDurationTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerPDURATION
     *
     * @param mixed $expectedResult
     */
    public function testPDURATION($expectedResult, array $args): void
    {
        $result = Financial::PDURATION(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerPDURATION(): array
    {
        return require 'tests/data/Calculation/Financial/PDURATION.php';
    }
}
