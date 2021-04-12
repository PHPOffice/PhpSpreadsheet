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
    public function testCONFIDENCE($expectedResult, ...$args): void
    {
        $result = Statistical::CONFIDENCE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerCONFIDENCE(): array
    {
        return require 'tests/data/Calculation/Statistical/CONFIDENCE.php';
    }
}
