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
        self::assertSame('x', TextData::TRIMNONPRINTABLE('x'));
        self::assertSame('x y', TextData::TRIMSPACES('x    y'));
        self::assertSame(48, TextData::ASCIICODE('0'));
        self::assertSame('abc', TextData::CONCATENATE('a', 'b', 'c'));
        self::assertSame('$1.00', TextData::DOLLAR(1));
        self::assertEquals(2, TextData::SEARCHSENSITIVE('b', 'abc'));
        self::assertEquals(2, TextData::SEARCHINSENSITIVE('b', 'abc'));
        self::assertSame('1.00', TextData::FIXEDFORMAT(1));
        self::assertSame('xyz', TextData::LEFT('xyzw', 3));
        self::assertSame('yz', TextData::MID('xyzw', 2, 2));
        self::assertSame('zw', TextData::RIGHT('xyzw', 2));
        self::assertSame(4, TextData::STRINGLENGTH('xyzw'));
        self::assertSame('xyzw', TextData::LOWERCASE('Xyzw'));
        self::assertSame('XYZW', TextData::UPPERCASE('Xyzw'));
        self::assertSame('Xyzw', TextData::PROPERCASE('xyzw'));
        self::assertSame('xabw', TextData::REPLACE('xyzw', 2, 2, 'ab'));
        self::assertSame('xyzw', TextData::TEXTFORMAT('xyzw', '@'));
        self::assertEquals(3, TextData::VALUE('3'));
        self::assertEquals(3, TextData::NUMBERVALUE('3'));
        self::assertTrue(TextData::EXACT('3', '3'));
        self::assertSame('a,b,c', TextData::TEXTJOIN(',', true, 'a', 'b', 'c'));
        self::assertSame('aaa', TextData::builtinREPT('a', 3));
        self::assertSame('ayxw', TextData::SUBSTITUTE('xyxw', 'x', 'a', 1));
        self::assertSame('1', TextData::CHARACTER('49'));
        self::assertSame('0', TextData::CHARACTER('48'));
    }
}
