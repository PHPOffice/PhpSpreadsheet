<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImLog10Test extends TestCase
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
     * @dataProvider providerIMLOG10
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMLOG10($expectedResult, $value): void
    {
        $result = Engineering::IMLOG10($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMLOG10(): array
    {
        return require 'tests/data/Calculation/Engineering/IMLOG10.php';
    }

    /**
     * @dataProvider providerImLog10Array
     */
    public function testImLog10Array(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMLOG10({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImLog10Array(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.4301690032855-0.84743999682982i', '0.39794000867204-0.68218817692092i', '0.4301690032855-0.51693635701202i'],
                    ['0.15051499783199-1.0232822653814i', '-0.68218817692092i', '0.15051499783199-0.34109408846046i'],
                    ['0.15051499783199+1.0232822653814i', '0.68218817692092i', '0.15051499783199+0.34109408846046i'],
                    ['0.4301690032855+0.84743999682982i', '0.39794000867204+0.68218817692092i', '0.4301690032855+0.51693635701202i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
