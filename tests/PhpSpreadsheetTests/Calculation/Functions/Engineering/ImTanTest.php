<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImTanTest extends TestCase
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
     * @dataProvider providerIMTAN
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMTAN($expectedResult, $value): void
    {
        $result = Engineering::IMTAN($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMTAN(): array
    {
        return require 'tests/data/Calculation/Engineering/IMTAN.php';
    }

    /**
     * @dataProvider providerImTanArray
     */
    public function testImTanArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMTAN({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImTanArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.012322138255828-1.0055480118951i', '-0.98661429815143i', '0.012322138255828-1.0055480118951i'],
                    ['-0.27175258531951-1.0839233273387i', '-0.76159415595576i', '0.27175258531951-1.0839233273387i'],
                    ['-0.27175258531951+1.0839233273387i', '0.76159415595576i', '0.27175258531951+1.0839233273387i'],
                    ['-0.012322138255828+1.0055480118951i', '0.98661429815143i', '0.012322138255828+1.0055480118951i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
