<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImSqrtTest extends TestCase
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
     * @dataProvider providerIMSQRT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSQRT($expectedResult, $value): void
    {
        $result = Engineering::IMSQRT($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSQRT(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSQRT.php';
    }

    /**
     * @dataProvider providerImSqrtArray
     */
    public function testImSqrtArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSQRT({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSqrtArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.9199408686343-1.3587829855366i', '1.1180339887499-1.1180339887499i', '1.3587829855366-0.9199408686343i'],
                    ['0.45508986056223-1.0986841134678i', '0.70710678118655-0.70710678118655i', '1.0986841134678-0.45508986056223i'],
                    ['0.45508986056223+1.0986841134678i', '0.70710678118655+0.70710678118655i', '1.0986841134678+0.45508986056223i'],
                    ['0.9199408686343+1.3587829855366i', '1.1180339887499+1.1180339887499i', '1.3587829855366+0.9199408686343i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
