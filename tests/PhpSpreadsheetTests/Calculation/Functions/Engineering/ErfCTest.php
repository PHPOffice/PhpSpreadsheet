<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class ErfCTest extends TestCase
{
    const ERF_PRECISION = 1E-12;

    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerERFC
     *
     * @param mixed $expectedResult
     */
    public function testERFC($expectedResult, ...$args): void
    {
        $result = Engineering::ERFC(...$args);
        self::assertEquals($expectedResult, $result);
        self::assertEqualsWithDelta($expectedResult, $result, self::ERF_PRECISION);
    }

    public function providerERFC(): array
    {
        return require 'tests/data/Calculation/Engineering/ERFC.php';
    }
}
