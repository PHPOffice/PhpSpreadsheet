<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls\RC4;
use PHPUnit\Framework\TestCase;

class Rc4Test extends TestCase
{
    public function testRc4(): void
    {
        // following result confirmed at:
        // https://cryptii.com/pipes/rc4-encryption
        $key = "\x63\x72\x79\x70\x74\x69\x69";
        $string = 'The quick brown fox jumps over the lazy dog.';
        $rc4 = new RC4($key);
        $result = bin2hex($rc4->RC4($string));
        $expectedResult = '2ac2fecdd8fbb84638e3a4820eb205cc8e29c28b9d5d6b2ef974f311964971c90e8b9ca16467ef2dc6fc3520';
        self::assertSame($expectedResult, $result);
    }
}
