<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ArrayFormulaTest extends TestCase
{
    /**
     * @var string
     */
    private $compatibilityMode;

    /**
     * @var string
     */
    private $locale;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
        $calculation = Calculation::getInstance();
        $this->locale = $calculation->getLocale();
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
        $calculation = Calculation::getInstance();
        $calculation->setLocale($this->locale);
    }

    /**
     * @dataProvider providerArrayFormulae
     *
     * @param mixed $expectedResult
     */
    public function testArrayFormula(string $formula, $expectedResult): void
    {
        $result = Calculation::getInstance()->_calculateFormulaValue($formula);
        self::assertEquals($expectedResult, $result);
    }

    public function providerArrayFormulae(): array
    {
        return [
            [
                '=MAX(ABS({-3, 4, -2; 6, -3, -12}))',
                12,
            ],
            [
                '=SUM(SEQUENCE(3,3,0,1))',
                36,
            ],
            [
                '=IFERROR({5/2, 5/0}, MAX(ABS({-2,4,-6})))',
                [[2.5, 6]],
            ],
            [
                '=MAX(IFERROR({5/2, 5/0}, 2.1))',
                2.5,
            ],
            [
                '=IF(FALSE,{1,2,3},{4,5,6})',
                [[4, 5, 6]],
            ],
            [
                '=IFS(FALSE, {1,2,3}, TRUE, {4,5,6})',
                [[4, 5, 6]],
            ],
        ];
    }
}
