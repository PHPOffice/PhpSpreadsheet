<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Statistical;

use PhpOffice\PhpSpreadsheet\Calculation\Statistical;
use PHPUnit\Framework\TestCase;

class CorrelTest extends TestCase
{
    /**
     * @dataProvider providerCORREL
     */
    public function testCORREL(mixed $expectedResult, mixed $xargs, mixed $yargs): void
    {
        $result = Statistical\Trends::CORREL($xargs, $yargs);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-12);
    }

    public static function providerCORREL(): array
    {
        return require 'tests/data/Calculation/Statistical/CORREL.php';
    }
}
