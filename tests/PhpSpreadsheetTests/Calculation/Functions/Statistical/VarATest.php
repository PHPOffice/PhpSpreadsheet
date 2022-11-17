<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarATest extends TestCase
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
     * @dataProvider providerVARA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVARA($expectedResult, $values): void
    {
        $result = Statistical\Variances::VARA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVARA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARA.php';
    }

    /**
     * @dataProvider providerOdsVARA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsVARA($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical\Variances::VARA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsVARA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARA_ODS.php';
    }
}
