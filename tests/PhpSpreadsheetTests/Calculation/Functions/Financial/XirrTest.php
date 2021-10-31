<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class XirrTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerXIRR
     *
     * @param mixed $expectedResult
     * @param mixed $message
     */
    public function testXIRR($expectedResult, $message, ...$args): void
    {
        $result = Financial::XIRR(...$args);
        if (is_numeric($result) && is_numeric($expectedResult)) {
            if ($expectedResult != 0) {
                $frac = $result / $expectedResult;
                if ($frac > 0.999999 && $frac < 1.000001) {
                    $result = $expectedResult;
                }
            }
        }
        self::assertEquals($expectedResult, $result, $message);
    }

    public function providerXIRR(): array
    {
        return require 'tests/data/Calculation/Financial/XIRR.php';
    }
}
