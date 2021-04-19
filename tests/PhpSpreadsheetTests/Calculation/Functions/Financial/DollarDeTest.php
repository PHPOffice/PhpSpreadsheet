<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class DollarDeTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
    }

    /**
     * @dataProvider providerDOLLARDE
     *
     * @param mixed $expectedResult
     */
    public function testDOLLARDE($expectedResult, ...$args): void
    {
        $result = Financial::DOLLARDE(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDOLLARDE(): array
    {
        return require 'tests/data/Calculation/Financial/DOLLARDE.php';
    }
}
