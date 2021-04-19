<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class XNpvTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerXNPV
     *
     * @param mixed $expectedResult
     * @param mixed $message
     */
    public function testXNPV($expectedResult, $message, ...$args): void
    {
        $result = Financial::XNPV(...$args);
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

    public function providerXNPV(): array
    {
        return require 'tests/data/Calculation/Financial/XNPV.php';
    }
}
