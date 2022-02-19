<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheetTests\Custom\ComplexAssert;
use PHPUnit\Framework\TestCase;

class ImExpTest extends TestCase
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
     * @dataProvider providerIMEXP
     *
     * @param mixed $expectedResult
     * @param mixed $value
     */
    public function testIMEXP($expectedResult, $value): void
    {
        $result = Engineering::IMEXP($value);
        self::assertTrue(
            $this->complexAssert->assertComplexEquals($expectedResult, $result, self::COMPLEX_PRECISION),
            $this->complexAssert->getErrorMessage()
        );
    }

    public function providerIMEXP(): array
    {
        return require 'tests/data/Calculation/Engineering/IMEXP.php';
    }

    /**
     * @dataProvider providerImExpArray
     */
    public function testImExpArray(array $expectedResult, string $complex): void
    {
        $calculation = Calculation::getInstance();

        $formula = "=IMEXP({$complex})";
        $result = $calculation->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerImExpArray(): array
    {
        return [
            'row/column vector' => [
                [
                    ['-0.29472426558547-0.22016559792964i', '-0.80114361554693-0.59847214410396i', '-2.1777341321272-1.6268159541567i'],
                    ['0.19876611034641-0.30955987565311i', '0.54030230586814-0.8414709848079i', '1.4686939399159-2.2873552871788i'],
                    ['0.19876611034641+0.30955987565311i', '0.54030230586814+0.8414709848079i', '1.4686939399159+2.2873552871788i'],
                    ['-0.29472426558547+0.22016559792964i', '-0.80114361554693+0.59847214410396i', '-2.1777341321272+1.6268159541567i'],
                ],
                '{"-1-2.5i", "-2.5i", "1-2.5i"; "-1-i", "-i", "1-i"; "-1+i", "i", "1+1"; "-1+2.5i", "+2.5i", "1+2.5i"}',
            ],
        ];
    }
}
