<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Exception as SpException;
use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;
use PHPUnit\Framework\TestCase;

class PasswordHasherTest extends TestCase
{
    /**
     * @dataProvider providerHashPassword
     */
    public function testHashPassword(
        string $expectedResult,
        string $password,
        ?string $algorithm = null,
        ?string $salt = null,
        ?int $spinCount = null
    ): void {
        if ($expectedResult === 'exception') {
            $this->expectException(SpException::class);
        }
        if ($algorithm === null) {
            $result = PasswordHasher::hashPassword($password);
        } elseif ($salt === null) {
            $result = PasswordHasher::hashPassword($password, $algorithm);
        } elseif ($spinCount === null) {
            $result = PasswordHasher::hashPassword($password, $algorithm, $salt);
        } else {
            $result = PasswordHasher::hashPassword($password, $algorithm, $salt, $spinCount);
        }
        self::assertSame($expectedResult, $result);
    }

    public static function providerHashPassword(): array
    {
        return require 'tests/data/Shared/PasswordHashes.php';
    }
}
