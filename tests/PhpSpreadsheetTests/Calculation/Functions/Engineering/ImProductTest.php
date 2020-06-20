<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImProductTest extends TestCase
{
    const COMPLEX_PRECISION = 1E-8;

    /**
     * @var ComplexAssert
     */
    protected $complexAssert;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        $this->complexAssert = new ComplexAssert();
    }

    protected function tearDown(): void
    {
        $this->complexAssert = null;
    }

    /**
     * @dataProvider providerIMPRODUCT
     *
     * @param mixed $expectedResult
     */
    public function testIMPRODUCT($expectedResult, ...$args): void
    {
        $result = Engineering::IMPRODUCT(...$args);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMPRODUCT()
    {
        return require 'tests/data/Calculation/Engineering/IMPRODUCT.php';
    }
}
