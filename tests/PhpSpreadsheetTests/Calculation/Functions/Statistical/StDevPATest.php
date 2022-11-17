<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class StDevPATest extends TestCase
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
     * @dataProvider providerSTDEVPA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testSTDEVPA($expectedResult, $values): void
    {
        $result = Statistical\StandardDeviations::STDEVPA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerSTDEVPA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVPA.php';
    }

    /**
     * @dataProvider providerOdsSTDEVPA
     *
     * @param mixed $expectedResult
     * @param mixed $values
     */
    public function testOdsSTDEVPA($expectedResult, $values): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_OPENOFFICE);

        $result = Statistical\StandardDeviations::STDEVPA($values);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public function providerOdsSTDEVPA(): array
    {
        return require 'tests/data/Calculation/Statistical/STDEVPA_ODS.php';
    }
}
