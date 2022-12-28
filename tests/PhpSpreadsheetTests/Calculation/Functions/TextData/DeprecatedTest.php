<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\TextData;

use PhpOffice\PhpSpreadsheet\Calculation\TextData;
use PHPUnit\Framework\TestCase;

// Sanity tests for functions which have been moved out of TextData
// to their own classes. A deprecated version remains in TextData;
// this class contains cursory tests to ensure that those work properly.
// If Scrutinizer fails the PR because of these deprecations, I will
// remove this class from the PR.

class DeprecatedTest extends TestCase
{
    public function testDeprecated(): void
    {
        self::assertSame('x', /** @scrutinizer ignore-deprecated */ TextData::TRIMNONPRINTABLE('x'));
        self::assertSame('x y', /** @scrutinizer ignore-deprecated */ TextData::TRIMSPACES('x    y'));
        self::assertSame(48, /** @scrutinizer ignore-deprecated */ TextData::ASCIICODE('0'));
        self::assertSame('abc', /** @scrutinizer ignore-deprecated */ TextData::CONCATENATE('a', 'b', 'c'));
        self::assertSame('$1.00', /** @scrutinizer ignore-deprecated */ TextData::DOLLAR(1));
        self::assertEquals(2, /** @scrutinizer ignore-deprecated */ TextData::SEARCHSENSITIVE('b', 'abc'));
        self::assertEquals(2, /** @scrutinizer ignore-deprecated */ TextData::SEARCHINSENSITIVE('b', 'abc'));
        self::assertSame('1.00', /** @scrutinizer ignore-deprecated */ TextData::FIXEDFORMAT(1));
        self::assertSame('xyz', /** @scrutinizer ignore-deprecated */ TextData::LEFT('xyzw', 3));
        self::assertSame('yz', /** @scrutinizer ignore-deprecated */ TextData::MID('xyzw', 2, 2));
        self::assertSame('zw', /** @scrutinizer ignore-deprecated */ TextData::RIGHT('xyzw', 2));
        self::assertSame(4, /** @scrutinizer ignore-deprecated */ TextData::STRINGLENGTH('xyzw'));
        self::assertSame('xyzw', /** @scrutinizer ignore-deprecated */ TextData::LOWERCASE('Xyzw'));
        self::assertSame('XYZW', /** @scrutinizer ignore-deprecated */ TextData::UPPERCASE('Xyzw'));
        self::assertSame('Xyzw', /** @scrutinizer ignore-deprecated */ TextData::PROPERCASE('xyzw'));
        self::assertSame('xabw', /** @scrutinizer ignore-deprecated */ TextData::REPLACE('xyzw', 2, 2, 'ab'));
        self::assertSame('xyzw', /** @scrutinizer ignore-deprecated */ TextData::TEXTFORMAT('xyzw', '@'));
        self::assertEquals(3, /** @scrutinizer ignore-deprecated */ TextData::VALUE('3'));
        self::assertEquals(3, /** @scrutinizer ignore-deprecated */ TextData::NUMBERVALUE('3'));
        self::assertTrue(/** @scrutinizer ignore-deprecated */ TextData::EXACT('3', '3'));
        self::assertSame('a,b,c', /** @scrutinizer ignore-deprecated */ TextData::TEXTJOIN(',', true, 'a', 'b', 'c'));
        self::assertSame('aaa', /** @scrutinizer ignore-deprecated */ TextData::builtinREPT('a', 3));
        self::assertSame('ayxw', /** @scrutinizer ignore-deprecated */ TextData::SUBSTITUTE('xyxw', 'x', 'a', 1));
        self::assertSame('1', /** @scrutinizer ignore-deprecated */ TextData::CHARACTER('49'));
        self::assertSame('0', /** @scrutinizer ignore-deprecated */ TextData::CHARACTER('48'));
        self::assertSame('xyz', /** @scrutinizer ignore-deprecated */ TextData::RETURNSTRING('xyz'));
    }
}
