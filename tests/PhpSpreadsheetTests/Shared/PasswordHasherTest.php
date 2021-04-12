<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;
use PHPUnit\Framework\TestCase;

class PasswordHasherTest extends TestCase
{
    /**
     * @dataProvider providerHashPassword
     *
     * @param mixed $expectedResult
     */
    public function testHashPassword($expectedResult, ...$args): void
    {
        $result = PasswordHasher::hashPassword(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerHashPassword(): array
    {
        return require 'tests/data/Shared/PasswordHashes.php';
    }
}
