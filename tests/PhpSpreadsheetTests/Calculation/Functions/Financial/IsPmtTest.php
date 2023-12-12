<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class IsPmtTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerISPMT
     */
    public function testISPMT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('ISPMT', $expectedResult, $args);
    }

    public static function providerISPMT(): array
    {
        return require 'tests/data/Calculation/Financial/ISPMT.php';
    }
}
