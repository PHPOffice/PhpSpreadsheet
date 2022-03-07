<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImSecTest extends TestCase
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
     * @dataProvider providerIMSEC
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSEC($expectedResult, $value): void
    {
        $result = Engineering::IMSEC($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSEC(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSEC.php';
    }

    /**
     * @dataProvider providerImSecArray
     */
    public function testImSecArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSEC({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSecArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['0.089798602872122+0.13798100670997i', '0.16307123192998', '0.089798602872122-0.13798100670997i'],
                    ['0.49833703055519+0.59108384172105i', '0.64805427366389', '0.49833703055519-0.59108384172105i'],
                    ['0.49833703055519-0.59108384172105i', '0.64805427366389', '0.49833703055519+0.59108384172105i'],
                    ['0.089798602872122-0.13798100670997i', '0.16307123192998', '0.089798602872122+0.13798100670997i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
