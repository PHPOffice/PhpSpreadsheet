<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImPowerTest extends TestCase
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
     * @dataProvider providerIMPOWER
     *
     * @param mixed $expectedResult
     */
    public function testIMPOWER($expectedResult, ...$args): void
    {
        $result = Engineering::IMPOWER(...$args);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMPOWER(): array
    {
        return require 'tests/data/Calculation/Engineering/IMPOWER.php';
    }

    /**
     * @dataProvider providerImPowerArray
     */
    public function testImPowerArray(array $expectedResult, string $complex, string $real): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMPOWER({$complex}, {$real})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImPowerArray(): array
    {
        return [
            'matrix' => [
                [
                    ['-5.25+5i', '-2.8702659355016E-15+15.625i', '2.5625+52.5i'],
                    ['-3.6739403974421E-16+2i', '-1.836970198721E-16+i', '-4-4.8985871965894E-16i'],
                    ['-3.6739403974421E-16-2i', '-1.836970198721E-16-i', '-4+4.8985871965894E-16i'],
                    ['-5.25-5i', '-2.8702659355016E-15-15.625i', '2.5625-52.5i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
                '{2, 3, 4}',
            ],
        ];
    }
}
