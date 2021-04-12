<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class ChooseTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCHOOSE
     *
     * @param mixed $expectedResult
     */
    public function testCHOOSE($expectedResult, ...$args): void
    {
        $result = LookupRef::CHOOSE(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerCHOOSE(): array
    {
        return require 'tests/data/Calculation/LookupRef/CHOOSE.php';
    }
}
