<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImCotTest extends TestCase
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
     * @dataProvider providerIMCOT
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCOT($expectedResult, $value): void
    {
        $result = Engineering::IMCOT($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCOT(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCOT.php';
    }

    /**
     * @dataProvider providerImCotArray
     */
    public function testImCotArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMCOT({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImCotArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.012184711291981+0.99433328540776i', '1.0135673098126i', '0.012184711291981+0.99433328540776i'],
                    ['-0.2176215618544+0.86801414289593i', '1.3130352854993i', '0.2176215618544+0.86801414289593i'],
                    ['-0.2176215618544-0.86801414289593i', '-1.3130352854993i', '0.2176215618544-0.86801414289593i'],
                    ['-0.012184711291981-0.99433328540776i', '-1.0135673098126i', '0.012184711291981-0.99433328540776i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
