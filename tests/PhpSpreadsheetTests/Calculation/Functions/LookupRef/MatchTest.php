<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\LookupRef;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\LookupRef;
use PHPUnit\Framework\TestCase;

class MatchTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMATCH
     *
     * @param mixed $expectedResult
     */
    public function testMATCH($expectedResult, ...$args): void
    {
        $result = LookupRef::MATCH(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerMATCH(): array
    {
        return require 'tests/data/Calculation/LookupRef/MATCH.php';
    }
}
