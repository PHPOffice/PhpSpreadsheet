<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PHPUnit\Framework\TestCase;

class AccrintTest extends TestCase
{
    /**
     * @dataProvider providerACCRINT
     *
     * @param mixed $expectedResult
     */
    public function testACCRINT($expectedResult, ...$args): void
    {
        $result = Financial\Securities\AccruedInterest::periodic(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerACCRINT(): array
    {
        return require 'tests/data/Calculation/Financial/ACCRINT.php';
    }
}
