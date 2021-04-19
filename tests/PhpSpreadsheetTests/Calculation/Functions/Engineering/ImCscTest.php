<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImCscTest extends TestCase
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
     * @dataProvider providerIMCSC
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMCSC($expectedResult, $value): void
    {
        $result = Engineering::IMCSC($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMCSC(): array
    {
        return require 'tests/data/Calculation/Engineering/IMCSC.php';
    }
}
