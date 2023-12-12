<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AmorLincTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAMORLINC
     */
    public function testAMORLINC(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('AMORLINC', $expectedResult, $args);
    }

    public static function providerAMORLINC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORLINC.php';
    }
}
