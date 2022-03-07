<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImConjugateTest extends TestCase
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
     * @dataProvider providerIMCONJUGATE
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCONJUGATE($expectedResult, $value): void
    {
        $result = Engineering::IMCONJUGATE($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCONJUGATE(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCONJUGATE.php';
    }

    /**
     * @dataProvider providerImConjugateArray
     */
    public function testImConjugateArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCONJUGATE({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImConjugateArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-1+2.5i', '2.5i', '1+2.5i'],
                    ['-1+i', 'i', '1+i'],
                    ['-1-i', '-i', '1-i'],
                    ['-1-2.5i', '-2.5i', '1-2.5i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
