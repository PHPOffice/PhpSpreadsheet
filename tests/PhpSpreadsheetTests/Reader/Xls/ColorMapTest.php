<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls\Color\BIFF5;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color\BIFF8;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color\BuiltIn;
use PHPUnit\Framework\TestCase;

class ColorMapTest extends TestCase
{
    /**
     * @dataProvider colorMapProvider
     */
    public function testColorMap(int $index, string $expectedBiff5, string $expectedBiff8, string $expectedBuiltin): void
    {
        self::assertSame($expectedBiff5, BIFF5::lookup($index)['rgb']);
        self::assertSame($expectedBiff8, BIFF8::lookup($index)['rgb']);
        self::assertSame($expectedBuiltin, BuiltIn::lookup($index)['rgb']);
    }

    public static function colorMapProvider(): array
    {
        return [
            'default builtin' => [0x00, '000000', '000000', '000000'],
            'non-default builtin' => [0x02, '000000', '000000', 'FF0000'],
            'system window text color' => [0x40, '000000', '000000', '000000'],
            'system window background color' => [0x41, '000000', '000000', 'FFFFFF'],
            'same biff5/8' => [0x09, 'FFFFFF', 'FFFFFF', '000000'],
            'different biff5/8' => [0x29, '69FFFF', 'CCFFFF', '000000'],

        ];
    }
}
