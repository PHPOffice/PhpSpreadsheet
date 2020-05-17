<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class GeStepTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerGESTEP
     *
     * @param mixed $expectedResult
     */
    public function testGESTEP($expectedResult, ...$args)
    {
        $result = Engineering::GESTEP(...$args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerGESTEP()
    {
        return require 'tests/data/Calculation/Engineering/GESTEP.php';
    }
}
