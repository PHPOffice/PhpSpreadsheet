<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImLog2Test extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    /**
     * @var ComplexAssert
     */
    private $complexAssert;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $this->complexAssert = new ComplexAssert();
    }

    /**
     * @dataProvider providerIMLOG2
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMLOG2($expectedResult, $value): void
    {
        $result = Engineering::IMLOG2($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMLOG2(): array
    {
        return require 'tests/data/Calculation/Engineering/IMLOG2.php';
    }

    /**
     * @dataProvider providerImLog2Array
     */
    public function testImLog2Array(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMLOG2({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImLog2Array(): array
    {
        return [
            'row/column vector' => [
                [
                    ['1.4289904975638-2.8151347342002i', '1.3219280948874-2.2661800709136i', '1.4289904975638-1.717225407627i'],
                    ['0.5-3.3992701063704i', '-2.2661800709136i', '0.5-1.1330900354568i'],
                    ['0.5+3.3992701063704i', '2.2661800709136i', '0.5+1.1330900354568i'],
                    ['1.4289904975638+2.8151347342002i', '1.3219280948874+2.2661800709136i', '1.4289904975638+1.717225407627i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
