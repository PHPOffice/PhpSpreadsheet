<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImSinTest extends TestCase
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
     * @dataProvider providerIMSIN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMSIN($expectedResult, $value): void
    {
        $result = Engineering::IMSIN($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSIN(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSIN.php';
    }

    /**
     * @dataProvider providerImSinArray
     */
    public function testImSinArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSIN({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSinArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-5.1601436675797-3.2689394320795i', '-6.0502044810398i', '5.1601436675797-3.2689394320795i'],
                    ['-1.298457581416-0.63496391478474i', '-1.1752011936438i', '1.298457581416-0.63496391478474i'],
                    ['-1.298457581416+0.63496391478474i', '1.1752011936438i', '1.298457581416+0.63496391478474i'],
                    ['-5.1601436675797+3.2689394320795i', '6.0502044810398i', '5.1601436675797+3.2689394320795i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
