<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class BitOrTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerBITOR
     *
     * @param mixed $expectedResult
     * @param mixed[] $args
     */
    public function testBITOR($expectedResult, array $args): void
    {
        $result = Engineering::BITOR(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerBITOR()
    {
        return require 'tests/data/Calculation/Engineering/BITOR.php';
    }
}
