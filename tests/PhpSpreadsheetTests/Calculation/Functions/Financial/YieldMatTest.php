<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class YieldMatTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerYIELDMAT
     */
    public function testYIELDMAT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('YIELDMAT', $expectedResult, $args);
    }

    public static function providerYIELDMAT(): array
    {
        return require 'tests/data/Calculation/Financial/YIELDMAT.php';
    }
}
