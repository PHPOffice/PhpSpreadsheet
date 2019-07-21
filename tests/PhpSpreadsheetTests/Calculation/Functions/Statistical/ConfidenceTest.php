<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class ConfidenceTest extends TestCase
{
    public function setUp()
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
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerCONFIDENCE()
    {
        return require 'data/Calculation/Statistical/CONFIDENCE.php';
    }
}
