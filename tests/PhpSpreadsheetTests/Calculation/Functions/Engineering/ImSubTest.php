<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImSubTest extends TestCase
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
     * @dataProvider providerIMSUB
     *
     * @param mixed $expectedResult
     */
    public function testIMSUB($expectedResult, ...$args): void
    {
        $result = Engineering::IMSUB(...$args);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMSUB(): array
    {
        return require 'tests/data/Calculation/Engineering/IMSUB.php';
    }

    /**
     * @dataProvider providerImSubArray
     */
    public function testImSubArray(array $expectedResult, string $subidend, string $subisor): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMSUB({$subidend}, {$subisor})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImSubArray(): array
    {
        return [
            'matrix' => [
                [
                    ['1-7.5i', '-2-2.5i', '-1-4.5i'],
                    ['1-6i', '-2-i', '-1-3i'],
                    ['1-4i', '-2+i', '-1-i'],
                    ['1-2.5i', '-2+2.5i', '-1+0.5i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
                '{"-2+5i", 2, "2+2i"}',
            ],
        ];
    }
}
