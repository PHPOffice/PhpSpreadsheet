<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class LookupTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerLOOKUP
     *
     * @param mixed $expectedResult
     */
    public function testLOOKUP($expectedResult, ...$args): void
    {
        $result = LookupRef::LOOKUP(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerLOOKUP(): array
    {
        return require 'tests/data/Calculation/LookupRef/LOOKUP.php';
    }
}
