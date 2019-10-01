<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\MathTrig;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig;
use PHPUnit\Framework\TestCase;

class MultinomialTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerMULTINOMIAL
     *
     * @param mixed $expectedResult
     */
    public function testMULTINOMIAL($expectedResult, ...$args)
    {
        $result = MathTrig::MULTINOMIAL(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-12);
    }

    public function providerMULTINOMIAL()
    {
        return require 'data/Calculation/MathTrig/MULTINOMIAL.php';
    }
}
