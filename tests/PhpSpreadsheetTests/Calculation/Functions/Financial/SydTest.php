<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class SydTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerSYD
     */
    public function testSYD(mixed $expectedResult, array $args): void
    {
        $this->runTestCase('SYD', $expectedResult, $args);
    }

    public static function providerSYD(): array
    {
        return require 'tests/data/Calculation/Financial/SYD.php';
    }
}
