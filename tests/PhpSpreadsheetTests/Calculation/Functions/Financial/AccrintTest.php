<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AccrintTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerACCRINT
     */
    public function testACCRINT(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('ACCRINT', $expectedResult, $args);
    }

    public static function providerACCRINT(): array
    {
        return require 'tests/data/Calculation/Financial/ACCRINT.php';
    }
}
