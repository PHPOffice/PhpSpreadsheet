<?php

declare(strict_types=1);

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
        $hashx1 = $protection->getPassword();
        self::assertSame('foo', $hashx1, 'was not stored as-is, without hashing');
        self::assertFalse($protection->verify('foo'), 'setting already hashed password will not match');

        $protection->setPassword('foo');
        $hashx2 = $protection->getPassword();
        self::assertSame('CC40', $hashx2, 'was hashed');
        self::assertTrue($protection->verify('foo'), 'setting non-hashed password will hash it and not match');

        $protection->setAlgorithm(Protection::ALGORITHM_MD5);
        self::assertFalse($protection->verify('foo'), 'changing algorithm will not match anymore');

        $protection->setPassword('foo');
        $hash1 = $protection->getPassword();
        $protection->setPassword('foo');
        $hash2 = $protection->getPassword();

        self::assertSame(24, mb_strlen($hash1));
        self::assertSame(24, mb_strlen($hash2));
        self::assertNotSame($hash1, $hash2, 'was hashed with automatic salt');
        self::assertTrue($protection->verify('foo'), 'setting password again, will hash with proper algorithm and will match');
    }
}
