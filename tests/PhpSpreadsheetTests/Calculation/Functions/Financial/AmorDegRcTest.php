<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AmorDegRcTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerAMORDEGRC
     */
    public function testAMORDEGRC(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('AMORDEGRC', $expectedResult, $args);
    }

    public static function providerAMORDEGRC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORDEGRC.php';
    }
}
