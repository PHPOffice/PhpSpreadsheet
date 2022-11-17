<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class VarPATest extends TestCase
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
     * @dataProvider providerVARPA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testVARPA($expectedResult, $values): void
    {
        $result = Statistical\Variances::VARPA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerVARPA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARPA.php';
    }

    /**
     * @dataProvider providerOdsVARPA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsVARPA($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical\Variances::VARPA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsVARPA(): array
    {
        return require 'tests/data/Calculation/Statistical/VARPA_ODS.php';
    }
}
