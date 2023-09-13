<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AccrintMTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerACCRINTM
     */
    public function testACCRINTM(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('ACCRINTM', $expectedResult, $args);
    }

    public static function providerACCRINTM(): array
    {
        return require 'tests/data/Calculation/Financial/ACCRINTM.php';
    }
}
