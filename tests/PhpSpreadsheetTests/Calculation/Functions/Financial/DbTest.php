<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class DbTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerDB
     */
    public function testDB(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('DB', $expectedResult, $args);
    }

    public static function providerDB(): array
    {
        return require 'tests/data/Calculation/Financial/DB.php';
    }
}
