<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerADDRESS
     *
     * @param mixed $expectedResult
     */
    public function testADDRESS($expectedResult, ...$args): void
    {
        $result = LookupRef::cellAddress(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerADDRESS(): array
    {
        return require 'tests/data/Calculation/LookupRef/ADDRESS.php';
    }
}
