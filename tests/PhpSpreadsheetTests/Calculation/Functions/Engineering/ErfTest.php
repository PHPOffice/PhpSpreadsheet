<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ErfTest extends TestCase
{
    const ERF_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerERF
     *
     * @param mixed $expectedResult
     */
    public function testERF($expectedResult, ...$args): void
    {
        $result = Engineering::ERF(...$args);
        self::assertEquals($expectedResult, $result);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerERF(): array
    {
        return require 'tests/data/Calculation/Engineering/ERF.php';
    }
}
