<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImCosTest extends TestCase
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
     * @dataProvider providerIMCOS
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCOS($expectedResult, $value): void
    {
        $result = Engineering::IMCOS($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCOS(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCOS.php';
    }

    /**
     * @dataProvider providerImCosArray
     */
    public function testImCosArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCOS({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImCosArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['3.3132901461132-5.0910715229497i', 6.1322894796637, '3.3132901461132+5.0910715229497i'],
                    ['0.83373002513115-0.98889770576287i', 1.5430806348152, '0.83373002513115+0.98889770576287i'],
                    ['0.83373002513115+0.98889770576287i', 1.5430806348152, '0.83373002513115-0.98889770576287i'],
                    ['3.3132901461132+5.0910715229497i', 6.1322894796637, '3.3132901461132-5.0910715229497i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
