<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Protection;
use PHPUnit\Framework\TestCase;

class ProtectionTest extends TestCase
{
    public function testVerifyPassword(): void
    {
        $protection = new Protection();
        self::assertTrue($protection->verify('foo'), 'non-protected always pass');

        $protection->setSheet(true);
        self::assertTrue($protection->verify('foo'), 'no password will always pass 1');
        self::assertTrue($protection->verify('xyz'), 'no password will always pass 2');

        $protection->setPassword('foo', true);
        self::assertSame('foo', $protection->getPassword(), 'was not stored as-is, without hashing');
        self::assertFalse($protection->verify('foo'), 'setting already hashed password will not match');

        $protection->setPassword('foo');
        self::assertSame('CC40', $protection->getPassword(), 'was hashed');
        self::assertTrue($protection->verify('foo'), 'setting non-hashed password will hash it and not match');

        $protection->setAlgorithm(Protection::ALGORITHM_MD5);
        self::assertFalse($protection->verify('foo'), 'changing algorithm will not match anymore');

        $protection->setPassword('foo');
        $hash1 = $protection->getPassword();
        $protection->setPassword('foo');
        $hash2 = $protection->getPassword();

        self::assertSame(24, mb_strlen($hash1)); // @phpstan-ignore-line
        self::assertSame(24, mb_strlen($hash2)); // @phpstan-ignore-line
        self::assertNotSame($hash1, $hash2, 'was hashed with automatic salt');
        self::assertTrue($protection->verify('foo'), 'setting password again, will hash with proper algorithm and will match');
    }
}
