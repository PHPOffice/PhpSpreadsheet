<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Engineering;

use PhpOffice\PhpSpreadsheet\Calculation\Engineering;
use PHPUnit\Framework\TestCase;

class ParseComplexTest extends TestCase
{
    public function testParseComplex(): void
    {
        [$real, $imaginary, $suffix] = [1.23e-4, 5.67e+8, 'j'];

        $result = /** @scrutinizer ignore-deprecated */ Engineering::parseComplex('1.23e-4+5.67e+8j');
        self::assertArrayHasKey('real', $result);
        self::assertEquals($real, $result['real']);
        self::assertArrayHasKey('imaginary', $result);
        self::assertEquals($imaginary, $result['imaginary']);
        self::assertArrayHasKey('suffix', $result);
        self::assertEquals($suffix, $result['suffix']);
    }
}
