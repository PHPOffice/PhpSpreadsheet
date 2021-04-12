<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class AccrintMTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerACCRINTM
     *
     * @param mixed $expectedResult
     */
    public function testACCRINTM($expectedResult, ...$args): void
    {
        $result = Financial::ACCRINTM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerACCRINTM(): array
    {
        return require 'tests/data/Calculation/Financial/ACCRINTM.php';
    }
}
