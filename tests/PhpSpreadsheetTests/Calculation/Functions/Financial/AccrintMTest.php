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
    public function testACCRINTM($expectedResult, ...$args)
    {
        $result = Financial::ACCRINTM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerACCRINTM()
    {
        return require 'data/Calculation/Financial/ACCRINTM.php';
    }
}
