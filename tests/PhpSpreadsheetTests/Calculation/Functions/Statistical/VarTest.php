<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarTest extends TestCase
{
    /** @var string */
    private $compatibilityMode;

    protected function setUp(): void
    {
        $this->compatibilityMode = Functions::getCompatibilityMode();
    }

    protected function tearDown(): void
    {
        Functions::setCompatibilityMode($this->compatibilityMode);
    }

    /**
     * @dataProvider providerVAR
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVAR($expectedResult, $values): void
    {
        $result = Statistical\Variances::VAR($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVAR(): array
    {
        return require 'tests/data/Calculation/Statistical/VAR.php';
    }

    /**
     * @dataProvider providerOdsVAR
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsVAR($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical\Variances::VAR($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsVAR(): array
    {
        return require 'tests/data/Calculation/Statistical/VAR_ODS.php';
    }
}
