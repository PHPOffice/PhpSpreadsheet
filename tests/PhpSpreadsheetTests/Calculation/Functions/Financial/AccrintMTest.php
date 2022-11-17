<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class AccrintMTest extends TestCase
{
    /**
     * @dataProvider providerACCRINTM
     *
     * @param mixed $expectedResult
     */
    public function testACCRINTM($expectedResult, ...$args): void
    {
        $result = Financial\Securities\AccruedInterest::atMaturity(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerACCRINTM(): array
    {
        return require 'tests/data/Calculation/Financial/ACCRINTM.php';
    }
}
