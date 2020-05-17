<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ConfidenceTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerCONFIDENCE
     *
     * @param mixed $expectedResult
     */
    public function testCONFIDENCE($expectedResult, ...$args)
    {
        $result = Statistical::CONFIDENCE(...$args);
        $this->assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCONFIDENCE()
    {
        return require 'tests/data/Calculation/Statistical/CONFIDENCE.php';
    }
}
