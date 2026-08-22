<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

class AmorLincTest extends AllSetupTeardown
{
    #[\PHPUnit\Framework\Attributes\DataProvider('providerAMORLINC')]
    public function testAMORLINC(mixed $expectedResult, mixed ...$args): void
    {
        $this->runTestCase('AMORLINC', $expectedResult, $args);
    }

    public static function providerAMORLINC(): array
    {
        return require 'tests/data/Calculation/Financial/AMORLINC.php';
    }
}
